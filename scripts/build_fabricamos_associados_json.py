#!/usr/bin/env python3

from __future__ import annotations

import argparse
import json
import math
import re
import unicodedata
from collections import OrderedDict
from datetime import date, datetime
from pathlib import Path

import pandas as pd


SOURCE_SHEET = "Fabricamos IFAS"
LEGEND_COMPANIES = {
    "Legenda",
    "Nao associados e sem CBPF",
    "CBPF Vencido",
    "Dado nao encontrado",
    "N/A - Significa Nao se Aplica",
    "Ainda nao conseguimos o contato",
}
PLACEHOLDER_VALUES = {
    "",
    "nan",
    "n/a",
    "n/a - significa nao se aplica",
    "nao aplicavel",
    "nao se aplica",
    "nao possui",
}
COMPANY_REPLACEMENTS = {
    "cristalia produtos quimicos farmaceutico ltda.": "CRISTALIA PRODUTOS QUIMICOS FARMACEUTICOS Ltda.",
}
SPREADSHEET_RANGE_ARTIFACT_RE = re.compile(r"\+[A-Z]{1,3}\d+:[A-Z]{1,3}\d+")


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Converte a planilha do Fabricamos em JSON consolidado por fabricante."
    )
    parser.add_argument("input", help="Caminho para a planilha .xlsx")
    parser.add_argument("output", help="Caminho do JSON de saida")
    parser.add_argument(
        "--sheet",
        default=SOURCE_SHEET,
        help=f"Nome da planilha a ler. Padrao: {SOURCE_SHEET!r}",
    )
    return parser.parse_args()


def clean_scalar(value: object) -> str:
    if value is None:
        return ""
    if isinstance(value, float) and math.isnan(value):
        return ""
    if isinstance(value, pd.Timestamp):
        return value.strftime("%Y-%m-%d")
    if isinstance(value, datetime):
        return value.strftime("%Y-%m-%d")
    if isinstance(value, date):
        return value.isoformat()

    text = str(value)
    text = text.replace("\r", " ").replace("\n", " ")
    text = SPREADSHEET_RANGE_ARTIFACT_RE.sub("", text)
    text = re.sub(r"\s+", " ", text).strip()
    if re.fullmatch(r"\d{4}-\d{2}-\d{2} 00:00:00", text):
        return text[:10]
    return text


def normalize_key(value: str) -> str:
    ascii_text = unicodedata.normalize("NFKD", value).encode("ascii", "ignore").decode("ascii")
    return re.sub(r"\s+", " ", ascii_text.strip().lower())


def normalize_company_name(value: str) -> str:
    return COMPANY_REPLACEMENTS.get(normalize_key(value), value)


def is_placeholder(value: str) -> bool:
    return normalize_key(value) in PLACEHOLDER_VALUES


def clean_catalog_value(value: object) -> str:
    text = clean_scalar(value)
    return "" if is_placeholder(text) else text


def is_associated_status(value: str) -> bool:
    normalized = normalize_key(value)
    return bool(normalized) and normalized.startswith("associado")


def append_unique(target: list[str], value: str) -> None:
    if value and value not in target:
        target.append(value)


def preferred_substance_name(insumo: str, inn: str, dcb: str) -> str:
    if insumo and not is_placeholder(insumo):
        return insumo
    if dcb and not is_placeholder(dcb):
        return dcb
    if inn and not is_placeholder(inn):
        return inn
    return ""


def main() -> None:
    args = parse_args()
    input_path = Path(args.input)
    output_path = Path(args.output)

    df = pd.read_excel(input_path, sheet_name=args.sheet, header=None)
    raw_update_label = clean_scalar(df.iat[5, 11]) if df.shape[0] > 5 and df.shape[1] > 11 else ""

    rows = df.iloc[9:].copy()
    rows.columns = [
        "empresa",
        "associado",
        "processo",
        "origem",
        "insumo",
        "dcb",
        "inn",
        "cas",
        "ncm",
        "cbpf",
        "validade",
        "responsavel",
        "telefone",
        "email",
        "nome_depto",
        "telefone2",
        "email_site",
        "site",
    ]

    companies: OrderedDict[str, dict[str, object]] = OrderedDict()

    for _, row in rows.iterrows():
        company = normalize_company_name(clean_scalar(row["empresa"]))
        if not company or company in LEGEND_COMPANIES:
            continue

        associate = clean_scalar(row["associado"])
        if not is_associated_status(associate):
            continue

        company_key = normalize_key(company)
        item = companies.setdefault(
            company_key,
            {
                "company": company,
                "associate": "",
                "processes": [],
                "origins": [],
                "substances": [],
                "catalog_items": [],
                "_catalog_seen": set(),
                "responsible_name": "",
                "responsible_phone": "",
                "responsible_email": "",
                "source_sheet": args.sheet,
                "source_workbook": input_path.name,
                "source_updated_label": raw_update_label,
            },
        )

        process = clean_scalar(row["processo"])
        origin = clean_scalar(row["origem"])
        insumo = clean_catalog_value(row["insumo"])
        dcb = clean_catalog_value(row["dcb"])
        inn = clean_catalog_value(row["inn"])
        cas = clean_catalog_value(row["cas"])
        ncm = clean_catalog_value(row["ncm"])
        cbpf = clean_catalog_value(row["cbpf"])
        validade = clean_catalog_value(row["validade"])
        responsible_name = clean_scalar(row["responsavel"])
        responsible_phone = clean_scalar(row["telefone"])
        responsible_email = clean_scalar(row["email"])
        display_name = preferred_substance_name(insumo, inn, dcb)

        if associate and not item["associate"]:
            item["associate"] = associate
        append_unique(item["processes"], process)
        append_unique(item["origins"], origin)
        append_unique(item["substances"], display_name)

        if responsible_name and not item["responsible_name"]:
            item["responsible_name"] = responsible_name
        if responsible_phone and not item["responsible_phone"]:
            item["responsible_phone"] = responsible_phone
        if responsible_email and not item["responsible_email"]:
            item["responsible_email"] = responsible_email

        catalog_item = {
            "insumo": insumo,
            "dcb": dcb,
            "inn": inn,
            "cas": cas,
            "ncm": ncm,
            "cbpf": cbpf,
            "validade": validade,
            "display_name": display_name,
        }
        catalog_key = tuple(
            normalize_key(str(catalog_item[field]))
            for field in ("display_name", "insumo", "dcb", "inn", "cas", "ncm", "cbpf", "validade")
        )

        if any(catalog_item.values()) and catalog_key not in item["_catalog_seen"]:
            item["_catalog_seen"].add(catalog_key)
            item["catalog_items"].append(catalog_item)

    payload: list[dict[str, object]] = []
    for item in companies.values():
        item.pop("_catalog_seen", None)
        payload.append(item)

    output_path.parent.mkdir(parents=True, exist_ok=True)
    with output_path.open("w", encoding="utf-8") as handle:
        json.dump(payload, handle, ensure_ascii=False, indent=2)
        handle.write("\n")

    print(f"Exported {len(payload)} fabricantes to {output_path}")


if __name__ == "__main__":
    main()

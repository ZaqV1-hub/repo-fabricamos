#!/usr/bin/env python3

from __future__ import annotations

import argparse
import html
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

    text = html.unescape(str(value))
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


def normalize_header(value: object) -> str:
    text = normalize_key(clean_scalar(value))
    return re.sub(r"[^a-z0-9]+", " ", text).strip()


def find_header_row(dataframe: pd.DataFrame) -> int:
    for index in range(len(dataframe.index)):
        headers = {normalize_header(value) for value in dataframe.iloc[index].tolist()}
        if "empresa" in headers and "insumo" in headers:
            return index
    raise ValueError("Nao foi encontrada uma linha de cabecalho com Empresa e Insumo.")


def resolve_column_indexes(header_row: pd.Series) -> dict[str, int]:
    aliases = {
        "empresa": {"empresa"},
        "associado": {"associado"},
        "processo": {"processo"},
        "origem": {"origem"},
        "insumo": {"insumo"},
        "dcb": {"dcb"},
        "inn": {"inn"},
        "cas": {"cas"},
        "ncm": {"ncm"},
        "cbpf": {"cbpf", "certificado cbpf"},
        "validade": {"validade", "validade cbpf"},
        "responsavel": {"responsavel"},
        "telefone": {"telefone"},
        "email": {"email"},
    }
    normalized_headers = [normalize_header(value) for value in header_row.tolist()]
    indexes: dict[str, int] = {}
    for field, candidates in aliases.items():
        for index, header in enumerate(normalized_headers):
            if header in candidates:
                indexes[field] = index
                break
    for required in ("empresa", "associado", "processo", "origem", "insumo"):
        if required not in indexes:
            raise ValueError(f"Coluna obrigatoria ausente: {required}.")
    return indexes


def row_value(row: pd.Series, indexes: dict[str, int], field: str) -> object:
    index = indexes.get(field)
    return "" if index is None else row.iloc[index]


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

    with pd.ExcelFile(input_path) as workbook:
        sheet_name = args.sheet if args.sheet in workbook.sheet_names else workbook.sheet_names[0]
        df = pd.read_excel(workbook, sheet_name=sheet_name, header=None)
    header_index = find_header_row(df)
    column_indexes = resolve_column_indexes(df.iloc[header_index])
    raw_update_label = ""
    if df.shape[1] > 11:
        raw_update_label = clean_scalar(df.iat[header_index, df.shape[1] - 1])

    rows = df.iloc[header_index + 1 :].copy()

    companies: OrderedDict[str, dict[str, object]] = OrderedDict()

    for _, row in rows.iterrows():
        company = normalize_company_name(clean_scalar(row_value(row, column_indexes, "empresa")))
        if not company or company in LEGEND_COMPANIES:
            continue

        associate = clean_scalar(row_value(row, column_indexes, "associado"))
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

        process = clean_scalar(row_value(row, column_indexes, "processo"))
        origin = clean_scalar(row_value(row, column_indexes, "origem"))
        insumo = clean_catalog_value(row_value(row, column_indexes, "insumo"))
        dcb = clean_catalog_value(row_value(row, column_indexes, "dcb"))
        inn = clean_catalog_value(row_value(row, column_indexes, "inn"))
        cas = clean_catalog_value(row_value(row, column_indexes, "cas"))
        ncm = clean_catalog_value(row_value(row, column_indexes, "ncm"))
        cbpf = clean_catalog_value(row_value(row, column_indexes, "cbpf"))
        validade = clean_catalog_value(row_value(row, column_indexes, "validade"))
        responsible_name = clean_scalar(row_value(row, column_indexes, "responsavel"))
        responsible_phone = clean_scalar(row_value(row, column_indexes, "telefone"))
        responsible_email = clean_scalar(row_value(row, column_indexes, "email"))
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

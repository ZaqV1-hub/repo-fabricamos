import json
import tempfile
import unittest
from pathlib import Path

from scripts.build_fabricamos_associados_json import clean_scalar, main


class CleanScalarTest(unittest.TestCase):
    def test_removes_embedded_spreadsheet_range_artifacts(self) -> None:
        self.assertEqual(
            clean_scalar("Cloridrat+B85:E153o de midazolam"),
            "Cloridrato de midazolam",
        )

    def test_exports_associated_and_non_associated_companies(self) -> None:
        # A conferência de planilha é geral; a restrição de associados pertence
        # somente ao catálogo público, não à geração da base de importação.
        import pandas as pd
        import sys

        rows = [[None] * 12 for _ in range(3)]
        rows[0] = ['Empresa', 'Associado', 'Processo', 'Origem', 'Insumo', 'DCB', 'INN', 'CAS', 'NCM', 'Certificado (CBPF)', 'Validade CBPF', 'Observação']
        rows[1] = ['Associada Ltda.', 'Associado', 'Síntese', '', 'Insumo A'] + [None] * 7
        rows[2] = ['Não Associada Ltda.', 'Não Associado', 'Formulação', '', 'Insumo B'] + [None] * 7

        with tempfile.TemporaryDirectory() as temporary_directory:
            directory = Path(temporary_directory)
            workbook = directory / 'fabricamos.xlsx'
            output = directory / 'fabricamos.json'
            pd.DataFrame(rows).to_excel(workbook, sheet_name='Fabricamos IFAS', header=False, index=False)

            original_argv = sys.argv
            try:
                sys.argv = ['build_fabricamos_associados_json.py', str(workbook), str(output)]
                main()
            finally:
                sys.argv = original_argv

            companies = json.loads(output.read_text(encoding='utf-8'))

        self.assertEqual(['Associada Ltda.', 'Não Associada Ltda.'], [company['company'] for company in companies])

    def test_decodes_html_entities_in_company_names(self) -> None:
        self.assertEqual('Buschle & Lepper S/A', clean_scalar('Buschle &amp; Lepper S/A'))
        self.assertEqual('H & N Homeopatia', clean_scalar('H &Amp; N Homeopatia'))


if __name__ == "__main__":
    unittest.main()

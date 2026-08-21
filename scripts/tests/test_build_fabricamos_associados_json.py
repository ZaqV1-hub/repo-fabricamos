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

        rows = [[None] * 18 for _ in range(11)]
        rows[0] = list(range(18))
        rows[5][11] = 'Atualizada em 2026-08-21'
        rows[9] = ['Associada Ltda.', 'Associado', 'Síntese', '', 'Insumo A'] + [None] * 13
        rows[10] = ['Não Associada Ltda.', 'Não Associado', 'Formulação', '', 'Insumo B'] + [None] * 13

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


if __name__ == "__main__":
    unittest.main()

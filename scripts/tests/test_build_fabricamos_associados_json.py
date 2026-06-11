import unittest

from scripts.build_fabricamos_associados_json import (
    clean_scalar,
    normalize_company_name,
    normalize_substance_name,
    preferred_substance_name,
)


class CleanScalarTest(unittest.TestCase):
    def test_removes_embedded_spreadsheet_range_artifacts(self) -> None:
        self.assertEqual(
            clean_scalar("Cloridrat+B85:E153o de midazolam"),
            "Cloridrato de midazolam",
        )


class NormalizeCompanyNameTest(unittest.TestCase):
    def test_groups_known_company_aliases(self) -> None:
        self.assertEqual(
            normalize_company_name("Cristália Produtos Químicos Farmacêutico Ltda."),
            "CRISTALIA PRODUTOS QUIMICOS FARMACEUTICOS Ltda.",
        )
        self.assertEqual(
            normalize_company_name("Cristalia Produtos Quimicos Farmaceutico Ltda."),
            "CRISTALIA PRODUTOS QUIMICOS FARMACEUTICOS Ltda.",
        )
        self.assertEqual(
            normalize_company_name("Libbs Farmacêutica"),
            "LIBBS FARMACEUTICA Ltda.",
        )


class NormalizeSubstanceNameTest(unittest.TestCase):
    def test_standardizes_case_and_spacing(self) -> None:
        self.assertEqual(normalize_substance_name("açaí extrato seco"), "Açaí extrato seco")
        self.assertEqual(normalize_substance_name("L- isoleucina"), "L-isoleucina")
        self.assertEqual(
            normalize_substance_name("hamamélis hidrolato / extrato fluido / extrato mole"),
            "Hamamélis hidrolato / extrato fluido / extrato mole",
        )

    def test_prefers_first_available_substance_field(self) -> None:
        self.assertEqual(
            preferred_substance_name("colagenase", "Collagenase", "Colagenase"),
            "Colagenase",
        )
        self.assertEqual(
            preferred_substance_name("", "Infliximabe", "Infliximabe"),
            "Infliximabe",
        )


if __name__ == "__main__":
    unittest.main()

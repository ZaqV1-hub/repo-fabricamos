import unittest

from scripts.build_fabricamos_associados_json import clean_scalar


class CleanScalarTest(unittest.TestCase):
    def test_removes_embedded_spreadsheet_range_artifacts(self) -> None:
        self.assertEqual(
            clean_scalar("Cloridrat+B85:E153o de midazolam"),
            "Cloridrato de midazolam",
        )


if __name__ == "__main__":
    unittest.main()

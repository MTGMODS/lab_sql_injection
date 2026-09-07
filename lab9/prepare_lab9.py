#!/usr/bin/env python3
"""Підготовка навчальних архівів для лабораторної №9.

Створює папки, тестовий файл, ZIP з відомими паролями, словник і шаблон таблиці.
Паролі задаєш сам (вони нижче в EXPERIMENT). Скрипт НЕ підбирає паролі.
"""

from __future__ import annotations

import argparse
import csv
import os
import struct
import sys
import time
import zlib
from pathlib import Path


EXPERIMENT = {
    "A_digits": [
        ("A1_len1.zip", "1"),
        ("A2_len2.zip", "12"),
        ("A3_len3.zip", "123"),
        ("A4_len4.zip", "1234"),
        ("A5_len5.zip", "12345"),
        ("A6_len6.zip", "123456"),
    ],
    "B_mixed": [
        ("B3_len3.zip", "A1b"),
        ("B4_len4.zip", "A1b2"),
        ("B5_len5.zip", "A1b2C"),
    ],
    "C_layout": [
        ("C_gfhjkm.zip", "gfhjkm"),
    ],
}

SECRET_TEXT = (
    "Лабораторна робота №9\n"
    "Тестові дані для дослідження стійкості парольного захисту.\n"
    "Файл створено навчальним скриптом prepare_lab9.py.\n"
)

DICT_COMMON = [
    "1",
    "12",
    "123",
    "1234",
    "12345",
    "123456",
    "password",
    "qwerty",
    "admin",
    "12345678",
    "gfhjkm",
    "parol",
    "parol1",
    "A1b",
    "A1b2",
]

DICT_MIXED = ["A1b2C"]


def crc32_byte(crc: int, byte: int) -> int:
    return zlib.crc32(bytes([byte & 0xFF]), crc ^ 0xFFFFFFFF) ^ 0xFFFFFFFF


class ZipCrypto:
    def __init__(self, password: bytes) -> None:
        self.keys = [0x12345678, 0x23456789, 0x34567890]
        for b in password:
            self._update_keys(b)

    def _update_keys(self, b: int) -> None:
        self.keys[0] = crc32_byte(self.keys[0], b)
        self.keys[1] = (self.keys[1] + (self.keys[0] & 0xFF)) & 0xFFFFFFFF
        self.keys[1] = (self.keys[1] * 134775813 + 1) & 0xFFFFFFFF
        self.keys[2] = crc32_byte(self.keys[2], self.keys[1] >> 24)

    def encrypt(self, plaintext: bytes, crc: int) -> bytes:
        header = bytearray(os.urandom(11))
        header.append((crc >> 24) & 0xFF)
        out = bytearray()
        for b in bytes(header) + plaintext:
            temp = (self.keys[2] | 2) & 0xFFFF
            magic = ((temp * (temp ^ 1)) >> 8) & 0xFF
            out.append(b ^ magic)
            self._update_keys(b)
        return bytes(out)


def dos_time(ts: float | None = None) -> tuple[int, int]:
    t = time.localtime(ts if ts is not None else time.time())
    dostime = (t.tm_hour << 11) | (t.tm_min << 5) | (t.tm_sec // 2)
    dosdate = ((t.tm_year - 1980) << 9) | (t.tm_mon << 5) | t.tm_mday
    return dostime, dosdate


def write_zipcrypto_zip(zip_path: Path, inner_name: str, content: bytes, password: str) -> None:
    """Один файл у ZIP з традиційним ZipCrypto (його очікує більшість навчальних програм)."""
    pwd = password.encode("utf-8")
    name = inner_name.encode("utf-8")
    crc = zlib.crc32(content) & 0xFFFFFFFF
    encrypted = ZipCrypto(pwd).encrypt(content, crc)
    dostime, dosdate = dos_time()

    local = struct.pack(
        "<IHHHHHIIIHH",
        0x04034B50,
        20,
        0x0001,
        0,
        dostime,
        dosdate,
        crc,
        len(encrypted),
        len(content),
        len(name),
        0,
    )
    local += name + encrypted

    central = struct.pack(
        "<IHHHHHHIIIHHHHHII",
        0x02014B50,
        20,
        20,
        0x0001,
        0,
        dostime,
        dosdate,
        crc,
        len(encrypted),
        len(content),
        len(name),
        0,
        0,
        0,
        0,
        0,
        0,
    )
    central += name

    eocd = struct.pack(
        "<IHHHHIIH",
        0x06054B50,
        0,
        0,
        1,
        1,
        len(central),
        len(local),
        0,
    )
    zip_path.write_bytes(local + central + eocd)


def write_lines(path: Path, lines: list[str]) -> None:
    path.write_text("\n".join(lines) + "\n", encoding="utf-8")


def verify_zip(zip_path: Path, password: str, inner_name: str, expected: bytes) -> None:
    import zipfile

    with zipfile.ZipFile(zip_path) as zf:
        zf.setpassword(password.encode("utf-8"))
        got = zf.read(inner_name)
    if got != expected:
        raise RuntimeError(f"Перевірка не пройшла: {zip_path}")


def build_csv(path: Path) -> None:
    rows = [
        [
            "series",
            "archive",
            "password",
            "length",
            "alphabet_size",
            "space_N",
            "mode",
            "time_sec",
            "speed_pwd_per_sec",
            "found",
            "notes",
        ]
    ]
    specs = [
        ("A_digits", "A1_len1.zip", "1", 1, 10, 10),
        ("A_digits", "A2_len2.zip", "12", 2, 10, 100),
        ("A_digits", "A3_len3.zip", "123", 3, 10, 1000),
        ("A_digits", "A4_len4.zip", "1234", 4, 10, 10_000),
        ("A_digits", "A5_len5.zip", "12345", 5, 10, 100_000),
        ("A_digits", "A6_len6.zip", "123456", 6, 10, 1_000_000),
        ("B_mixed", "B3_len3.zip", "A1b", 3, 62, 62**3),
        ("B_mixed", "B4_len4.zip", "A1b2", 4, 62, 62**4),
        ("B_mixed", "B5_len5.zip", "A1b2C", 5, 62, 62**5),
        ("C_layout", "C_gfhjkm.zip", "gfhjkm", 6, 26, 26**6),
    ]
    for series, archive, password, length, alpha, space in specs:
        rows.append(
            [
                series,
                archive,
                password,
                str(length),
                str(alpha),
                str(space),
                "bruteforce",
                "",
                "",
                "",
                "",
            ]
        )
        rows.append(
            [
                series,
                archive,
                password,
                str(length),
                str(alpha),
                str(space),
                "dictionary",
                "",
                "",
                "",
                "",
            ]
        )
    with path.open("w", encoding="utf-8-sig", newline="") as fh:
        csv.writer(fh).writerows(rows)


def build_manifest(path: Path, out_dir: Path) -> None:
    lines = [
        "Маніфест навчальних архівів (паролі відомі заздалегідь).",
        f"Каталог: {out_dir}",
        "",
        "Серія A — лише цифри, повний перебір з алфавітом 0-9.",
    ]
    for name, pwd in EXPERIMENT["A_digits"]:
        lines.append(f"  {name:16}  пароль={pwd}")
    lines += [
        "",
        "Серія B — три категорії (A-Z, a-z, 0-9), повний перебір з алфавітом 62 символи.",
    ]
    for name, pwd in EXPERIMENT["B_mixed"]:
        lines.append(f"  {name:16}  пароль={pwd}")
    lines += [
        "",
        "Серія C — українське слово при латинській розкладці: «пароль» → gfhjkm.",
        "  Перебір: алфавіт a-z, довжина 6.",
    ]
    for name, pwd in EXPERIMENT["C_layout"]:
        lines.append(f"  {name:16}  пароль={pwd}")
    lines += [
        "",
        "Словник:",
        "  dictionary/dict_with_mixed.txt     — є A1b2C (контрольний прогін, має знайти)",
        "  dictionary/dict_without_mixed.txt  — немає A1b2C (має НЕ знайти B5)",
        "",
        "У програмі відновлення відкривай лише ці власні архіви.",
        "Для серії A обмеж алфавіт цифрами, для B — літери+цифри, для C — малі літери.",
        "Час записуй у results/results_template.csv.",
    ]
    write_lines(path, lines)


def prepare(out_dir: Path) -> None:
    plain = out_dir / "plain"
    archives = out_dir / "archives"
    dictionary = out_dir / "dictionary"
    results = out_dir / "results"
    for folder in (plain, archives, dictionary, results):
        folder.mkdir(parents=True, exist_ok=True)

    secret_path = plain / "secret.txt"
    secret_path.write_text(SECRET_TEXT, encoding="utf-8")
    secret_bytes = secret_path.read_bytes()
    inner_name = "secret.txt"

    created = []
    for series, items in EXPERIMENT.items():
        series_dir = archives / series
        series_dir.mkdir(parents=True, exist_ok=True)
        for filename, password in items:
            zip_path = series_dir / filename
            write_zipcrypto_zip(zip_path, inner_name, secret_bytes, password)
            verify_zip(zip_path, password, inner_name, secret_bytes)
            created.append(zip_path)

    write_lines(dictionary / "dict_with_mixed.txt", DICT_COMMON + DICT_MIXED)
    write_lines(dictionary / "dict_without_mixed.txt", DICT_COMMON)
    build_csv(results / "results_template.csv")
    build_manifest(results / "manifest.txt", out_dir)

    print(f"Готово: {out_dir.resolve()}")
    print(f"Архівів створено і перевірено: {len(created)}")
    for path in created:
        print(f"  {path.relative_to(out_dir)}")
    print("Словники: dictionary/dict_with_mixed.txt, dictionary/dict_without_mixed.txt")
    print("Таблиця:  results/results_template.csv  (відкрий в Excel)")
    print("Паролі:   results/manifest.txt")


def main() -> int:
    parser = argparse.ArgumentParser(description="Підготовка власних тестових архівів для лабораторної №9")
    parser.add_argument(
        "--out",
        default=str(Path(__file__).resolve().parent / "Lab9_experiment"),
        help="Каталог експерименту",
    )
    args = parser.parse_args()
    prepare(Path(args.out))
    return 0


if __name__ == "__main__":
    sys.exit(main())

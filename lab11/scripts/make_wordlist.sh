#!/usr/bin/env bash
# Створює невеликий словник для ГАРАНТОВАНОЇ демонстрації злому слабкого пароля.
# Додай свій слабкий пароль першим аргументом — він точно потрапить у словник.
#
# Використання:
#   ./make_wordlist.sh <твій_слабкий_пароль>
# Приклад:
#   ./make_wordlist.sh 12345678
set -euo pipefail

WEAK="${1:?Вкажи свій слабкий пароль, напр.: ./make_wordlist.sh 12345678}"
OUT="demo_wordlist.txt"

{
  printf '%s\n' \
    00000000 11111111 12345678 123456789 1234567890 \
    password password1 password123 qwerty123 admin1234 \
    "$WEAK"
} | sort -u > "$OUT"

echo "[*] Готово: $OUT ($(wc -l < "$OUT") паролів)."
echo "[*] Далі: aircrack-ng -w $OUT -b <BSSID> handshake-01.cap"

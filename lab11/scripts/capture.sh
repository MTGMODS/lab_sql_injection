#!/usr/bin/env bash
# Переводить вбудований Wi-Fi у режим монітора і слухає ОДНУ вашу точку доступу.
# Запускати в Kali від root. Тільки власна точка доступу!
#
# Використання:
#   sudo ./capture.sh <BSSID> <КАНАЛ> [ІНТЕРФЕЙС]
# Приклад:
#   sudo ./capture.sh AA:BB:CC:DD:EE:FF 6 wlan0
set -euo pipefail

BSSID="${1:?Вкажи BSSID своєї точки доступу (MAC), напр. AA:BB:CC:DD:EE:FF}"
CH="${2:?Вкажи канал (число), напр. 6}"
IFACE="${3:-wlan0}"

if [[ $EUID -ne 0 ]]; then
  echo "Запусти через sudo." >&2
  exit 1
fi

echo "[*] Зупиняю служби, що заважають режиму монітора..."
airmon-ng check kill

echo "[*] Вмикаю режим монітора на $IFACE..."
airmon-ng start "$IFACE" >/dev/null

# Визначаємо імʼя монітор-інтерфейсу (зазвичай wlanXmon).
MON="$(iw dev 2>/dev/null | awk '/Interface/ {print $2}' | grep -m1 -E 'mon' || true)"
MON="${MON:-${IFACE}mon}"
echo "[*] Монітор-інтерфейс: $MON"

echo "[*] Слухаю $BSSID на каналі $CH."
echo "[*] ЗАРАЗ підключи свій другий пристрій до точки доступу (або вимкни/увімкни на ньому Wi-Fi),"
echo "    щоб зʼявився handshake. Чекай напис 'WPA handshake: $BSSID' угорі праворуч."
echo "[*] Файли: handshake-01.cap. Зупинка: Ctrl+C."
echo

airodump-ng --bssid "$BSSID" -c "$CH" -w handshake "$MON"

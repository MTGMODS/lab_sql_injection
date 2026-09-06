#!/bin/sh
set -eu

ROOT=$(CDPATH= cd -- "$(dirname "$0")" && pwd)
cd "$ROOT"

if ! command -v php >/dev/null 2>&1; then
  echo "PHP не знайдено. На Ubuntu/Debian:"
  echo "  sudo apt update && sudo apt install -y php-cli php-sqlite3"
  exit 1
fi

php -r 'exit(extension_loaded("pdo_sqlite") ? 0 : 1);' || {
  echo "Немає розширення pdo_sqlite. На Ubuntu/Debian:"
  echo "  sudo apt install -y php-sqlite3"
  exit 1
}

mkdir -p "$ROOT/vulnerable/data" "$ROOT/secure/data"
chmod 777 "$ROOT/vulnerable/data" "$ROOT/secure/data" 2>/dev/null || true

echo "Вразлива версія: http://0.0.0.0:8080"
echo "Захищена версія: http://0.0.0.0:8081"
echo "Зупинка: Ctrl+C"
echo

php -S 0.0.0.0:8080 -t "$ROOT/vulnerable" &
PID1=$!
php -S 0.0.0.0:8081 -t "$ROOT/secure" &
PID2=$!

trap 'kill "$PID1" "$PID2" 2>/dev/null; wait' INT TERM EXIT
wait

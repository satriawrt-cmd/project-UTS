"""
==============================================================
INVENTORY & SALES MANAGEMENT SYSTEM - PYTHON LAUNCHER
Web Programming 2 | UTS Integrated Laravel Project
==============================================================
Script ini mempermudah Anda menjalankan aplikasi Laravel cukup
dengan mengetik: python run.py di terminal.
"""

import os
import sys
import subprocess
import webbrowser
import time

# Ensure UTF-8 output on Windows terminal
if hasattr(sys.stdout, 'reconfigure'):
    try:
        sys.stdout.reconfigure(encoding='utf-8')
    except Exception:
        pass

def print_banner():
    print("=" * 60)
    print("  [+] INVENTORY & SALES MANAGEMENT SYSTEM")
    print("  Framework : Laravel 12 & PHP 8.2 & MySQL")
    print("  Launcher  : Python Runner")
    print("=" * 60)

def check_php():
    try:
        result = subprocess.run(["php", "-v"], stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True)
        if result.returncode != 0:
            print("[!] PHP tidak ditemukan di sistem!")
            return False
        first_line = result.stdout.split("\n")[0]
        print(f"[OK] PHP Terdeteksi : {first_line}")
        return True
    except FileNotFoundError:
        print("[!] Perintah 'php' tidak ditemukan di PATH sistem Anda.")
        return False

def run_migrations():
    print("\n[*] Menjalankan Database Migration & Seeding...")
    subprocess.run(["php", "artisan", "migrate", "--seed"])

def run_tests():
    print("\n[*] Menjalankan Pengujian Otomatis (PHPUnit)...")
    subprocess.run(["php", "artisan", "test"])

def run_server(port=8000):
    url = f"http://127.0.0.1:{port}"
    print(f"\n[>] Menjalankan Server Laravel di: {url}")
    print("[*] Membuka browser secara otomatis...")
    print("[i] Tekan Ctrl + C di terminal untuk menghentikan server kapan saja.\n")

    # Buka browser otomatis setelah 1.5 detik
    def open_browser():
        time.sleep(1.5)
        webbrowser.open(url)

    import threading
    threading.Thread(target=open_browser, daemon=True).start()

    try:
        subprocess.run(["php", "artisan", "serve", f"--port={port}"])
    except KeyboardInterrupt:
        print("\n[!] Server dihentikan oleh pengguna. Selesai.")

def main():
    print_banner()

    if not check_php():
        sys.exit(1)

    # Argument handling
    if len(sys.argv) > 1:
        arg = sys.argv[1].lower()
        if arg in ["--test", "-t", "test"]:
            run_tests()
            return
        elif arg in ["--seed", "-s", "seed"]:
            run_migrations()
            return
        elif arg in ["--help", "-h", "help"]:
            print("\nPanduan Perintah:")
            print("  python run.py          -> Menjalankan server dan otomatis membuka browser")
            print("  python run.py test     -> Menjalankan 40 unit & feature tests")
            print("  python run.py seed     -> Menjalankan migrasi dan seeder database")
            return

    # Default action: Jalankan server
    run_server()

if __name__ == "__main__":
    main()

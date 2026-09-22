.PHONY: up down restart build logs bash migrate fresh key storage setup ps clear routes reset

# ─── Docker ────────────────────────────────────────────────
up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose restart

build:
	docker compose up -d --build

logs:
	docker compose logs -f

ps:
	docker compose ps

# ─── Laravel ───────────────────────────────────────────────
bash:
	docker compose exec app bash

key:
	docker compose exec app php artisan key:generate

migrate:
	docker compose exec app php artisan migrate

fresh:
	docker compose exec app php artisan migrate:fresh

storage:
	docker compose exec app php artisan storage:link

clear:
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan view:clear

routes:
	docker compose exec app php artisan route:list --except-vendor

# ─── Setup Pertama Kali (jalankan sekali saja) ─────────────
setup:
	@echo "🚀 Memulai setup Folio..."
	docker compose up -d --build
	@echo "⏳ Menunggu database siap..."
	sleep 20
	@echo "🔑 Generate app key..."
	docker compose exec app php artisan key:generate
	@echo "🗄️  Menjalankan migrasi..."
	docker compose exec app php artisan migrate
	@echo "🔗 Membuat storage link..."
	docker compose exec app php artisan storage:link
	@echo "🧹 Clear cache..."
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	@echo ""
	@echo "✅ Setup selesai!"
	@echo "🌐 Web    : http://localhost:8080"
	@echo "📱 Android: http://10.0.2.2:8080/api/v1"

# ─── Reset total (HAPUS SEMUA DATA) ────────────────────────
reset:
	docker compose down -v
	docker compose up -d --build
	sleep 20
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate:fresh
	docker compose exec app php artisan storage:link
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	@echo "✅ Reset selesai!"

---
layout: doc
---

# Lokale Entwicklung

Fiov wurde mit Laravel und Livewire entwickelt und benötigt daher eine PHP-Umgebung zur Ausführung.
Es gibt verschiedene Möglichkeiten, eine PHP-Entwicklungsumgebung einzurichten, aber wenn macOS oder Windows verwendet wird, ist wahrscheinlich [Laravel Herd](https://herd.laravel.com/) der einfachste Weg.
Wenn Linux verwendet wird, ist dir vermutlich bekannt, wie du eine eigene Umgebung einrichten kannst 😉

Zusätzlich werden [Node.js und npm](https://nodejs.org/) benötigt, um die Frontend-Assets zu erstellen.

## Lokale Ausführung
1.  **Repository klonen:**

    ```bash
    git clone https://github.com/PascalKleindienst/fiov.git
    cd fiov
    ```

2.  **PHP-Abhängigkeiten installieren:**

    ```bash
    composer install
    ```
3.  **Node.js-Abhängigkeiten installieren:**

    ```bash
    npm install
    ```

4. **Installer ausführen:**

   Führe den Installer aus und folge seinen Anweisungen. Dadurch wird die Datenbank eingerichtet, ein neuer Admin-Benutzer und ein Standard-Wallet erstellt sowie die Frontend-Assets kompiliert.

   ```bash
    php artisan fiov:init
    ```
5. **(Optional) Datenbank mit Testdaten befüllen:**

   Falls die Datenbank mit einigen Demo-Daten befüllt werden soll, kann folgender Befehl verwendet werden:

    ```bash
     php artisan db:seed --class=Database\\Seeders\\DemoDataSeeder
    ```

7. **Entwicklungsserver starten:**

   Ein Entwicklungsserver wird unter `http://localhost:8000` gestartet
   ```bash
   $ composer dev
   
   > Composer\Config::disableProcessTimeout
   [vite] 
   [vite] > dev
   [vite] > vite
   [vite]
   [logs]
   [logs]    INFO  Tailing application logs.                        Press Ctrl+C to exit  
   [logs]                                                Use -v|-vv to show more details  
   [queue]
   [queue]    INFO  Processing jobs from the [default] queue.  
   [queue]
   [vite]
   [vite]   VITE v6.2.0  ready in 310 ms
   [vite]
   [vite]   ➜  Local:   http://localhost:5173/
   [vite]   ➜  Network: use --host to expose
   [vite]
   [vite]   LARAVEL v12.28.1  plugin v1.2.0
   [vite]
   [vite]   ➜  APP_URL: http://localhost:8000
   [server]
   [server]    INFO  Server running on [http://127.0.0.1:8000].  
   [server]
   [server]   Press Ctrl+C to stop the server
   [server]
    ```

## Testing, Linting, etc.
Es stehen verschiedene Skripte zur Verfügung, die beim Testen, Linting usw. helfen. Diese sind alle Teil der `composer.json`-Datei. Weitere Informationen können im Abschnitt `scripts` der `composer.json`-Datei eingesehen werden.

```bash
composer test               # Die gesamte Test-Suite wird ausgeführt (d.h. alles mit test:*)
composer test:lint          # Linting wird ausgeführt (Pint)
composer test:rector        # Rector wird mit --dry-run ausgeführt, d.h. es wird geprüft, ob Änderungen vorgenommen werden müssen
composer test:type-coverage # Type Coverage wird ausgeführt
composer test:types         # Statische Analyse wird ausgeführt (PHPStan)
composer test:unit          # Unit-/Feature-Tests werden ausgeführt

composer format             # Alle vom Linter gefundenen Probleme werden behoben
composer rector             # Alle von Rector gefundenen Probleme werden behoben
```

## Dokumentation
Falls zur Dokumentation beigetragen werden soll, kann diese im `docs`-Verzeichnis gefunden werden. Sie ist in Markdown geschrieben und verwendet [vitepress](https://vitepress.vuejs.org/).
Um die VitePress-Instanz zu starten, kann folgender Befehl verwendet werden:

```bash
npm run docs:dev

> docs:dev
> vitepress dev docs

  vitepress v1.6.4

  ➜  Local:   http://localhost:5173/
  ➜  Network: use --host to expose
  ➜  press h to show help
```

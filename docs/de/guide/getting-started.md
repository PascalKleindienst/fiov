---
layout: doc
---

# Erste Schritte

## Anforderungen

Fiov stellt die folgenden Anforderungen:

- [Alle Anforderungen von Laravel 12](https://laravel.com/docs/12.x#server-requirements) – PHP >= 8.3 mit erforderlichen Erweiterungen
- Eine der von Laravel unterstützten Datenbanken.
- Wenn Sie Fiov aus dem Quellcode erstellen, stellen Sie sicher, dass Sie Composer, Git und Node.js >= 20 mit npm installiert haben.

## Installation
### Verwendung des vorkompilierten Archivs

::: warning TODO
Die Installation aus einem vorkompilierten Archiv wird derzeit nicht unterstüzt. Wir arbeiten daran, dies in der Zukunft zu verbessern.
:::

### Aus dem Quellcode erstellen

```bash
cd <FIOV_ROOT_DIR>
git clone https://github.com/PascalKleindienst/fiov.git .
composer install --optimize-autoloader --no-dev
npm install
php artisan fiov:init 

# Folge den Anweisungen des Installers.
# Dadurch wird die Datenbank eingerichtet, ein neuer Admin-Benutzer
# und eine Standard-Wallet erstellt und die Frontend-Assets erstellt.

php artisan serve
```

Die Anwendung ist nun verfügbar unter `http://127.0.0.1:8000`.

::: tip Hinweis
Mit `php artisan serve` können Sie die Anwendung lokal unter `http://127.0.0.1:8000` ausführen. Dies ist aber nur für Entwicklungszwecke geeignet.
Aus Performancegründen empfehlen wir, die Anwendung mit einem Webserver wie Nginx oder Apache zu betreiben und auf das `public`-Verzeichnis der Anwendung zuzugreifen.
:::

## Einstellungen
Die Konfiguration von Fiov wird in der `.env`-Datei im Stammverzeichnis des Projekts gespeichert, welche während des Installationsprozesses aus der `.env.example`-Datei erstellt wird. 
Die Werte können jederzeit an die eigene Umgebung angepasst werden.

### Emails konfigurieren
Obwohl Fiov auch ohne Mailer funktionieren kann, empfiehlt es sich einen Mailer zu konfigurieren, da manche Features wie Benutzereinladungen den Mailversand erfordern.

Die Einstellungen für den Mailer sind in der `.env`-Datei im Stammverzeichnis des Projekts gespeichert und beginnen mit `MAIL_`. Eine Beispiel Konfiguration für Google Mail könnte wie folgt aussehen:

```ini
MAIL_MAILER=smtp                            # Typ des Mailers, Standard: smtp
MAIL_FROM_ADDRESS="hello@example.com"       # Die Absenderadresse
MAIL_FROM_NAME="${APP_NAME}"                # Der Name des Senders, hier wird der Wert aus der APP_NAME-Variable verwendet
MAIL_HOST=smtp.gmail.com                    # Der Mail Host/Servername
MAIL_PORT=587                               # Der SMTP Port
MAIL_USERNAME=Ihreadresse@googlemail.com    # Der SMTP Benutzername
MAIL_PASSWORD=test                          # Ein generiertes App-Passwort
```

Falls keine E-Mail-abhängigen Features benötigt werden, kann der `MAIL_MAILER`-Wert auf `log` oder `array` gesetzt werden und der Rest der mailer-bezogenen Werte leer gelassen werden.

## Update
::: danger Datenbank sichern
Vor jeder Aktualisierung der Fiov-Version solle ein Backup der Datenbank erstellt werden.
:::

### Update einer Installation aus dem Quellcode
Wenn Fiov aus dem Quellcode installiert wurde, kann das Update wie folgt durchgeführt weren

```bash
git pull origin main
composer install --optimize-autoloader --no-dev
npm install
php artisan fiov:init
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```


## Fehlerbehebung
Bei der Installation und dem Betrieb von Fiov können verschiedene technische Probleme auftreten, die eine systematische Herangehensweise erfordern.
Eine detaillierte Anleitung zur allgemeinen Fehlerbehebung kann unter [Fehlerbehebung](./troubleshooting.md) eingesehen werden.  
Im Folgenden werden einige der häufigsten Installationsprobleme und deren Lösungsansätze beschrieben:

### Berechtigungsprobleme
Es muss sichergestellt werden, dass die Verzeichnisse `storage` und `bootstrap/cache` beschreibbar sind. Diese Ordner werden von Laravel zur Speicherung von Cache-Dateien, Session-Daten, kompilierten Templates und Log-Dateien verwendet. Ohne entsprechende Schreibberechtigungen können verschiedene Anwendungsfunktionen nicht ordnungsgemäß ausgeführt werden.
```bash
chmod -R 775 storage bootstrap/cache
```

Zusätzlich sollte überprüft werden, dass der Webserver-Benutzer (meist `www-data`, `apache` oder `nginx`) Eigentümer dieser Verzeichnisse ist:
```bash
chown -R www-data:www-data storage bootstrap/cache
```

### Composer Speicherlimit
Falls Speicherlimit-Probleme während der Installation von PHP-Abhängigkeiten auftreten, kann das Speicherlimit temporär aufgehoben werden. Dies ist besonders bei größeren Projekten oder auf Servern mit begrenztem Arbeitsspeicher erforderlich. Das Problem äußert sich meist durch Fehlermeldungen wie "Fatal error: Allowed memory size exhausted".
```bash
COMPOSER_MEMORY_LIMIT=-1 composer install
```
Alternativ kann auch das PHP-Speicherlimit in der `php.ini` dauerhaft erhöht werden:
```ini
memory_limit = 512M
```

### Node.js Version
Es muss sichergestellt werden, dass eine kompatible Node.js Version (20.x oder höher) verwendet wird. Ältere Versionen können zu Kompilierungsfehlern bei den Frontend-Assets führen oder moderne JavaScript-Features nicht unterstützen. Die aktuell installierte Version kann mit folgendem Befehl überprüft werden:
```bash
node --version
npm --version
```
Für die Verwaltung verschiedener Node.js Versionen wird die Verwendung von nvm (Node Version Manager) empfohlen, welches das einfache Wechseln zwischen verschiedenen Node.js Versionen ermöglicht:
```bash
# nvm installieren (unter Linux/macOS)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash

# Neueste LTS Version installieren und verwenden
nvm install --lts
nvm use --lts
```


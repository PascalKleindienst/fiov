---
layout: doc
---

# Fehlerbehebung

Niemand ist perfekt und Dinge gehen manchmal schief. Alles halb so wild, hier sind einige häufige Probleme und ihre Lösungen:

## Erste Schritte

Wenn ein Fehler auftritt, sollte man zuerst in den Logs unter `storage/logs/laravel.log` schauen. Oftmals sieht man dann schon was genau schief gelaufen ist oder wo der Fehler auftritt.
Die Fehlermeldungen sind auch wichtig, falls ein [Ticket](https://github.com/pascalkleindienst/fiov/issues) erstellt werden muss.

::: danger Schaue immer zuerst ins Log
Der erste Schritt bei einem Fehler sollte immer ein Blick ins Logfile `storage/logs/laravel.log` sein 
:::

Als Nächstes sollte die Browserkonsole auf JavaScript-Fehler überprüft werden. Der "Netwerk" Tab in der Entwickler-Konsole kann auch auf fehlgeschlagene Anfragen geprüft werden.

Ein weiterer Schritt kann sein, den den Cache zu leeren, Abhängigkeiten neu zu installieren und die Frontend-Assets neu zu kompilieren.
Im Folgenden sind einige Befehle, die dabei helfen können:

```bash
# Vendor Verzeichnis löschen und Abhängigkeiten neu installieren
rm -rf vendor && composer install

# Frontend-Assets neu kompilieren
rm -rf node_modules && npm install && npm run build

# Application Cache löschen
php artisan cache:clear

# Config-Cache löschen
php artisan config:clear
```

### Fiov Status prüfen

Mit dem Befehl `php artisan fiov:status` kann man den Status von Fiov prüfen. Dabei werden alle relevanten Informationen ausgegeben, die helfen können, das Problem zu identifizieren.

```bash
php artisan fiov:status
```

![Fiov Status](../../assets/images/fiov-status.png)


## Häufige Probleme

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

## Um Hilfe fragen

Falls du alleine nicht weiter kommst, kannst du auf [Github](https://github.com/pascalkleindienst/fiov/issues) ein Ticket erstellen. Denke daran, höflich und geduldig zu sein.

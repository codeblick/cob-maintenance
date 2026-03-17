# Shopware Wartungsmodus Login mit automatischer IP-Freischaltung

Dieses Projekt ist ein **Plugin für Shopware 6** und erweitert die **standardmäßige Wartungsmodus-Seite von Shopware** um ein Login-Formular.

Wenn sich ein Benutzer mit gültigen **Shopware-Admin-Zugangsdaten** anmeldet, wird seine aktuelle IP-Adresse automatisch freigeschaltet. Danach kann die Seite trotz aktivem Wartungsmodus normal genutzt werden.

## Ziel

Während Wartungsarbeiten sollen berechtigte Personen (z. B. Entwickler, Projektleitung, QA) weiterhin Zugriff auf den Shop haben, ohne den Wartungsmodus global zu deaktivieren.

Das Plugin wurde ursprünglich für **platform.sh-Projekte** gebaut, da dort keine **.htaccess-Passwörter** für einen einfachen Wartungszugang gesetzt werden können.

## Funktionen

- Erweiterung der Standard-Wartungsseite um ein Login-Feld
- Authentifizierung über bestehende Shopware-Admin-Logins
- Automatische Freischaltung der aktuellen Client-IP nach erfolgreichem Login
- Weiterleitung zurück auf die gewünschte Shop-Seite
- Kein separater Benutzerstamm nötig

## So funktioniert es

1. Wartungsmodus ist aktiv.
2. Ein Benutzer ruft den Shop auf und sieht die Wartungsseite.
3. Über das zusätzliche Login-Formular meldet er sich mit seinen Admin-Daten an.
4. Bei erfolgreicher Prüfung wird seine IP-Adresse in die erlaubte Liste aufgenommen.
5. Der Benutzer erhält Zugriff auf den Shop, obwohl der Wartungsmodus weiterhin aktiv ist.

## Voraussetzungen

- Shopware 6
- Ein gültiger Admin-Benutzer in Shopware
- Berechtigung, den Wartungsmodus bzw. die IP-Allowlist zu verwalten

## Typischer Einsatz

- Live-Debugging im Wartungsmodus
- Content-Abnahme durch interne Teams
- Schnellzugriff für Entwickler ohne manuelles Eintragen der IP

## Hinweis zur Anmeldung

Die Anmeldung erfolgt **mit den normalen Shopware-Admin-Zugangsdaten**. Es ist **kein separates Wartungsmodus-Passwort** erforderlich.

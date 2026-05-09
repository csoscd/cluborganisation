# ClubOrganisation – Feature-Übersicht

**Version:** 2.3.0
**Joomla:** 5.x / 6.x

---

## Zielgruppe

ClubOrganisation richtet sich an Vereine, Verbände und Organisationen jeder Größe, die ihre Mitgliederverwaltung innerhalb von Joomla abwickeln möchten – von der Datenpflege über DSGVO-konforme Anonymisierung bis hin zur maschinell lesbaren Datenausgabe über REST-API.

---

## Hauptfunktionen Backend

### 1. Personen-Verwaltung

Erfassung vollständiger Stammdaten:
- Anrede, Vor-/Nachname, Geburtsname, Zweitname
- Geburtsdatum, Sterbedatum, Adresse, Kontaktdaten
- Mitgliedsnummer (eindeutig), Foto-Upload
- Verknüpfung mit Joomla-Benutzer
- Automatische Joomla-User-Erstellung mit konfigurierbarer Benutzergruppe und optionalem E-Mail-Versand der Zugangsdaten
- Filterung nach Name, Mitgliedsnummer, Status

### 2. Mitgliedschafts-Verwaltung

Zeitraum-basierte Mitgliedschaften:
- Begin-Datum (Pflicht), End-Datum (NULL = aktiv)
- Maximal eine aktive Mitgliedschaft gleichzeitig
- Überschneidungsprüfung beim Speichern
- Mehrere historische Mitgliedschaften pro Person möglich
- Kategorisierung nach Mitgliedschaftstyp

**Abhängige Mitgliedschaftstypen (neu in 2.3.0):**
- Mitgliedschaftstypen können als „abhängig" markiert werden (z. B. „Familienmitglied" hängt von „Familienmitglied (zahlend)" ab)
- Bei Anlage einer Mitgliedschaft mit abhängigem Typ muss die übergeordnete Mitgliedschaft ausgewählt werden
- Endet die übergeordnete Mitgliedschaft, werden alle abhängigen Mitgliedschaften ohne früheres Enddatum automatisch mitbeendet
- Wird das Enddatum der übergeordneten Mitgliedschaft entfernt, wird es auch bei abhängigen Mitgliedschaften mit gleichem Enddatum entfernt
- Warnhinweis im Bearbeitungsformular bei Mitgliedschaften, die abhängige Mitgliedschaften haben

### 3. Bankdaten-Verwaltung

Verschlüsselte Speicherung sensibler Zahlungsdaten:
- Kontoinhaber, IBAN, BIC – alle mit AES-256-CBC verschlüsselt
- Schlüssel ausschließlich in PHP-Session (nie in Datenbank)
- Entsperr-Maske vor Listenzugriff
- **Canary-Mechanismus**: deterministische Schlüsselvalidierung ohne Heuristik
- Key Rotation: alle Datensätze mit neuem Schlüssel neu verschlüsseln
- IBAN-Maskierung in der Übersichtsliste
- Schreibgeschützte Detailansicht

### 4. Mitgliedschaftsgebühren & Beitragsübersicht

- Zeitbasierte Gebühren pro Mitgliedschaftstyp (beliebig viele, ab Datum gültig)
- Backend: automatische Jahres-Beitragsübersicht mit Summen
- Frontend: öffentliche Darstellung aktueller und zukünftiger Beiträge

### 5. BwPostman-Synchronisation

3-stufiger Prozess:
1. Aktive Mitglieder als Subscriber anlegen/reaktivieren
2. Inaktive Mitglieder in BwPostman archivieren
3. Mailinglist-Zuordnung verwalten

Konfigurierbar: Feldmapping, Gender-Mapping, Mailinglist-Auswahl. Transaction-Safe.

### 6. Migration Tool

Import aus der Clubmanagement-Komponente:
- Mapping aller Felder
- Username → User-ID Konvertierung
- Validierung und Fehlerprotokoll
- Transaction-Safe mit Rollback

### 7. DSGVO Cleanup

Automatische Anonymisierung nach konfigurierbarer Frist (1–20 Jahre):
- Zeigt nur Personen mit ausschließlich beendeten Mitgliedschaften
- Schützt vor versehentlicher Anonymisierung aktiver Mitglieder
- Anonymisiert: Namen, Kontaktdaten, Geburtsdatum, E-Mail
- Löscht vollständig: alle Bankverbindungen
- Setzt Active-Flag auf 0
- DSGVO Artikel 17 konform, Transaction-Safe

### 9. Statistik (neu in 2.1.0)

Neue Admin-Seite mit grafischen und tabellarischen Auswertungen:
- Liniendiagramm Mitgliederentwicklung letztes Jahr (monatlich, Stichtag letzter Monatstag)
- Liniendiagramm Mitgliederentwicklung aktuelles Jahr (Farbe #f29838)
- Balkendiagramm Mitgliederentwicklung seit konfiguriertem Startjahr (Farbe #f29838)
- Tabelle Mitgliederstruktur je Mitgliedschaftsart (aktuelles Jahr, Vorjahr, Differenz)
- Vergleichs-Balkendiagramm Mitgliederstruktur (aktuelles Jahr: #f29838, Vorjahr: #132d6a)
- Tabelle Altersstruktur in 5 Gruppen (< 18, 18–29, 30–49, 50–65, > 65)
- Tabelle Mitgliedschaftsdauer in 6 Gruppen (≤ 1 J. bis > 20 J.)

Das Startjahr für das Langzeit-Diagramm ist in der Komponentenkonfiguration unter dem neuen Abschnitt **Reporting** einstellbar.

### 10. Stammdaten

- Anreden: Herr, Frau, Divers (erweiterbar, sortierbar)
- Mitgliedschaftstypen: Einzelmitglied, Einzelmitglied (reduziert), Familienmitglied (zahlend), Familienmitglied (erweiterbar)

---

## REST-API (seit 2.0.0)

Joomla-konforme REST-API für den maschinell lesbaren Export:

**Endpunkt:** `GET /api/index.php/v1/cluborganisation/members`  
**Auth:** `X-Joomla-Token` Header  
**Berechtigung:** `core.manage` auf `com_cluborganisation`

Steuerbar über Query-Parameter:

| Parameter | Default | Funktion |
|---|---|---|
| `active_memberships` | 1 | Nur Personen mit aktiver Mitgliedschaft |
| `include_banks` | 0 | Entschlüsselte Bankdaten einschließen |
| `active_banks` | 1 | Nur aktuelle Bankverbindungen |
| `encryption_key` | – | Pflicht bei `include_banks=1` |

Personen ohne passende Mitgliedschaft werden bei gefilterter Ausgabe vollständig ausgeschlossen.

**Benötigt:** Plugin `plg_webservices_cluborganisation` installiert und aktiviert.

---

## Frontend-Features

### Aktive Mitglieder

Öffentliche Mitgliederliste mit konfigurierbaren Spalten (Mitgliedsnummer, Name, Adresse, Kontakt, Geburtsdatum, Mitgliedschaftstyp, Eintrittsjahr), Sortierung und Pagination.

### Eintritte/Austritte

Jahresbasierte Übersicht, umschaltbar zwischen Eintritten und Austritten. Basis: erste/letzte Mitgliedschaft.

### Mitgliedschaftsgebühren

Öffentliche Darstellung der aktuellen Beiträge pro Typ, inkl. zukünftiger Änderungen.

### Mein Profil / Meine Mitgliedschaften

Nur für eingeloggte Benutzer. Zeigt eigene Stammdaten und vollständige Mitgliedschafts-Historie.

### Konfiguration über Menu Item Parameter

Alle Frontend-Views unterstützen konfigurierbare Optionen im Joomla Menu Item:
- Spaltenanzeige (einzeln ein-/ausblendbar)
- Primäre und sekundäre Sortierung
- Anzahl pro Seite
- Bewegungstyp und Jahr (für Eintritte/Austritte)

---

## Benutzer-Rollen

| Rolle | Rechte |
|---|---|
| **Administrator** | Vollzugriff, DSGVO Cleanup, Migration, Konfiguration |
| **Manager** | Lesen und Bearbeiten, keine Löschung, keine Konfiguration |
| **Mitglied (eingeloggt)** | Eigenes Profil und Mitgliedschafts-Historie im Frontend |
| **Öffentlich** | Aktive Mitglieder, Eintritte/Austritte, Gebühren |

---

## Typische Workflows

### Neues Mitglied aufnehmen

```
1. Personen → Neu → Stammdaten erfassen → Speichern
2. Mitgliedschaften → Neu → Person auswählen, Typ wählen,
   Begin-Datum setzen → Speichern
3. Optional: Bankverbindung → Neu → Schlüssel eingeben,
   IBAN/BIC/Kontoinhaber erfassen → Speichern
```

### Mitgliedschaftswechsel

```
1. Aktuelle Mitgliedschaft öffnen → End-Datum setzen → Speichern
2. Neue Mitgliedschaft → Neu → Neuen Typ und Begin-Datum setzen
   (Überschneidungsprüfung schützt vor Fehlern)
```

### DSGVO-Bereinigung

```
1. Optionen → DSGVO-Frist konfigurieren (z.B. 3 Jahre)
2. DSGVO Cleanup öffnen → Liste prüfen
3. Personen auswählen → Anonymisieren → Bestätigen
```

### Daten per API exportieren

```bash
# Token aus Benutzerprofil
curl -H "X-Joomla-Token: <TOKEN>" \
     https://example.com/api/index.php/v1/cluborganisation/members

# Mit Bankdaten
curl -H "X-Joomla-Token: <TOKEN>" \
     "https://example.com/api/index.php/v1/cluborganisation/members\
?include_banks=1&encryption_key=MeinSchluessel"
```

---

## Nützliche SQL-Abfragen

**Aktuelle Mitgliederzahl:**
```sql
SELECT COUNT(DISTINCT p.id) AS aktive_mitglieder
FROM #__cluborganisation_persons p
JOIN #__cluborganisation_memberships m ON p.id = m.person_id
WHERE m.begin <= CURDATE()
  AND (m.end IS NULL OR m.end >= CURDATE())
  AND p.active = 1
  AND p.deceased IS NULL;
```

**Verteilung nach Mitgliedschaftstyp:**
```sql
SELECT t.title, COUNT(*) AS anzahl
FROM #__cluborganisation_memberships m
JOIN #__cluborganisation_membershiptypes t ON m.type = t.id
WHERE m.begin <= CURDATE()
  AND (m.end IS NULL OR m.end >= CURDATE())
GROUP BY t.title ORDER BY anzahl DESC;
```

**Altersstruktur:**
```sql
SELECT
    CASE
        WHEN TIMESTAMPDIFF(YEAR, p.birthday, CURDATE()) < 30 THEN 'Unter 30'
        WHEN TIMESTAMPDIFF(YEAR, p.birthday, CURDATE()) < 50 THEN '30–49'
        WHEN TIMESTAMPDIFF(YEAR, p.birthday, CURDATE()) < 65 THEN '50–64'
        ELSE 'Über 65'
    END AS altersgruppe,
    COUNT(*) AS anzahl
FROM #__cluborganisation_persons p
JOIN #__cluborganisation_memberships m ON p.id = m.person_id
WHERE m.begin <= CURDATE()
  AND (m.end IS NULL OR m.end >= CURDATE())
  AND p.active = 1
GROUP BY altersgruppe;
```

---

**Stand:** März 2026 · **Version:** 2.3.0

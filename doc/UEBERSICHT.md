# ClubOrganisation - Feature-Übersicht

**Version:** 1.0.0  
**Joomla:** 5.x / 6.x

---

## 🎯 Zielgruppe

ClubOrganisation richtet sich an:
- **Vereine** jeder Größe
- **Verbände** mit Mitgliederverwaltung
- **Organisationen** mit wiederkehrenden Mitgliedschaften
- **Clubs** mit Mitgliederbeiträgen

---

## ✨ Hauptfunktionen

### 1. Personen-Verwaltung

**Stammdaten erfassen:**
- Anrede, Vor-/Nachname, Geburtsname
- Geburtsdatum, Sterbedatum
- Adresse (Straße, PLZ, Stadt, Land)
- Kontakt (Telefon, Mobil, E-Mail)
- Mitgliedsnummer (eindeutig)
- Foto-Upload

**Erweiterte Funktionen:**
- Verknüpfung mit Joomla-Benutzer
- Entry Year / Exit Year (automatisch aus Mitgliedschaften)
- Active Flag (aktiv/anonymisiert)
- Filterung nach Name, Mitgliedsnummer, Status
- Sortierung konfigurierbar
- Batch-Operationen

**Use Cases:**
- Mitgliederdatenbank führen
- Kontaktdaten verwalten
- Mitgliederfotos hinterlegen
- Historie nachvollziehen (Entry/Exit Year)

### 2. Mitgliedschafts-Verwaltung

**Zeitraum-basiert:**
- Begin-Datum (Pflichtfeld)
- End-Datum (optional, NULL = aktiv)
- Automatische Überschneidungsprüfung
- Mehrere Mitgliedschaften pro Person möglich
- Maximal eine aktive gleichzeitig

**Kategorisierung:**
- Mitgliedschaftstyp (Einzelmitglied, Familie, etc.)
- Beitragshöhe
- Beschreibung

**Erweiterte Funktionen:**
- Filterung nach Person, Typ, Zeitraum
- Historie aller Mitgliedschaften
- Automatische Berechnung Entry/Exit Year
- Prüfung auf aktive Mitgliedschaften

**Use Cases:**
- Mitgliedschaftswechsel verwalten
- Familienmitgliedschaften abbilden
- Reduzierte Beiträge für bestimmte Gruppen
- Historie lückenlos dokumentieren

### 3. Bankdaten-Verwaltung

**Verschlüsselte Speicherung:**
- Kontoinhaber (verschlüsselt)
- IBAN (verschlüsselt)
- BIC (verschlüsselt)
- AES-256-CBC Verschlüsselung
- Schlüssel nie in Datenbank

**Sicherheit:**
- Session-basierter Zugriff
- Schlüssel muss eingegeben werden
- Automatische Entschlüsselung für autorisierte Benutzer
- Vollständige Löschung bei Anonymisierung

**Use Cases:**
- SEPA-Lastschriften vorbereiten
- Beitragszahlungen verwalten
- DSGVO-konform speichern

### 4. Migration Tool

**Import aus Clubmanagement:**
- Mapping von Feldern
- Username → User-ID Konvertierung
- Validierung der Daten
- Fehlerprotokoll
- Transaction-Safe (Rollback bei Fehlern)

**Daten-Mapping:**
- Personen (alle Felder)
- Mitgliedschaften (Zeiträume)
- Automatische Verknüpfungen

**Use Cases:**
- Umstieg von alter Clubmanagement-Komponente
- Datenmigrationen
- Einmalige Datenübernahme

### 5. DSGVO Cleanup

**Automatische Anonymisierung:**
- Konfigurierbare Frist (1-20 Jahre, Standard: 3)
- Zeigt Personen mit beendeten Mitgliedschaften
- Prüft auf aktive Mitgliedschaften (Schutz)
- Filtert bereits anonymisierte Personen
- Transaction-Safe mit Audit-Trail

**Was wird anonymisiert:**
- Namen → "Anonymisiert Person [ID]"
- Kontaktdaten → Anonymisiert/gelöscht
- Geburtsdatum → 1970-01-01
- E-Mail → anonymisiert_[ID]@deleted.local
- Bankdaten → Vollständig gelöscht
- Active Flag → 0

**DSGVO-Compliance:**
- Artikel 17: Recht auf Vergessenwerden
- Irreversible Anonymisierung
- Statistiken bleiben erhalten
- Mitgliedschaften bleiben erhalten

**Use Cases:**
- Rechtskonforme Datenlöschung
- Automatische Bereinigung
- DSGVO-Anforderungen erfüllen

### 6. Stammdaten-Verwaltung

**Anreden:**
- Herr, Frau, Divers
- Erweiterbar
- Sortierung konfigurierbar

**Mitgliedschaftstypen:**
- Einzelmitglied
- Einzelmitglied (reduziert)
- Familienmitglied (zahlend)
- Familienmitglied
- Erweiterbar
- Beitragsklassen definierbar

**Use Cases:**
- Vereinsspezifische Anpassungen
- Beitragsstruktur abbilden
- Mitgliedschaftskategorien pflegen

---

## 🌐 Frontend-Features

### 1. Aktive Mitglieder

**Öffentliche Mitgliederliste:**
- Alle aktiven Vereinsmitglieder
- Konfigurierbare Spaltenanzeige
- Sortierung nach verschiedenen Kriterien
- Pagination mit konfigurierbarem Limit

**Konfigurierbare Felder:**
- Mitgliedsnummer
- Anrede
- Vor-/Nachname
- Adresse (Straße, PLZ, Stadt)
- Kontakt (Telefon, Mobil, E-Mail)
- Geburtsdatum
- Mitgliedschaftstyp
- Mitgliedschaft seit (Begin)
- Erste Mitgliedschaft (Entry Year)
- Letzte Mitgliedschaft (Exit Year)

**Menu Item Optionen:**
- Welche Spalten zeigen
- Primäre/Sekundäre Sortierung
- Anzahl pro Seite

**Use Cases:**
- Öffentliches Mitgliederverzeichnis
- Kontaktliste für Mitglieder
- Transparenz für Vereinsmitglieder

### 2. Eintritte/Austritte

**Jahres-basierte Übersicht:**
- Umschaltbar zwischen Eintritten und Austritten
- Jahr konfigurierbar
- Entry Year / Exit Year basiert
- Zeigt erste/letzte Mitgliedschaft

**Konfigurierbare Felder:**
- Wie "Aktive Mitglieder"
- Zusätzlich: Datum der ersten/letzten Mitgliedschaft

**Menu Item Optionen:**
- Bewegungstyp (Eintritte/Austritte)
- Jahr (Standard: aktuelles Jahr)
- Spalten und Sortierung

**Use Cases:**
- Mitgliederentwicklung nachvollziehen
- Statistiken für Jahresberichte
- Neue Mitglieder begrüßen
- Austritte dokumentieren

### 3. Mein Profil

**Persönliche Übersicht:**
- Stammdaten des Mitglieds
- Alle verknüpften Mitgliedschaften
- Aktuelle und beendete Mitgliedschaften
- Kontaktdaten

**Zugriff:**
- Nur für eingeloggte Benutzer
- Automatische Verknüpfung über user_id
- Nur eigene Daten sichtbar

**Use Cases:**
- Mitglieder prüfen ihre Daten
- Self-Service für Mitglieder
- Transparenz über Mitgliedschaftsstatus

### 4. Meine Mitgliedschaften

**Mitgliedschafts-Historie:**
- Chronologische Liste
- Aktiv/Beendet-Status
- Typ und Zeitraum
- Alle Mitgliedschaften des Benutzers

**Use Cases:**
- Historie einsehen
- Mitgliedschaftswechsel nachvollziehen
- Dokumentation für Mitglieder

---

## 🔧 Konfiguration

### Komponenten-Optionen

**DSGVO:**
- Jahre bis Cleanup (1-20, Standard: 3)
- Schwellwert für Anonymisierung

**Berechtigungen:**
- ACL für Benutzergruppen
- Komponenten-Level Rechte
- Asset-Level Rechte (pro Datensatz)

**Verschlüsselung:**
- Schlüsselgenerierung
- Session-Management

### Menu Item Parameter

**Aktive Mitglieder:**
```
Display Options:
├── Show Member No (Ja/Nein)
├── Show Salutation (Ja/Nein)
├── Show Firstname (Ja/Nein)
├── Show Lastname (Ja/Nein)
├── Show Address (Ja/Nein)
├── Show Zip (Ja/Nein)
├── Show City (Ja/Nein)
├── Show Telephone (Ja/Nein)
├── Show Mobile (Ja/Nein)
├── Show Email (Ja/Nein)
├── Show Birthday (Ja/Nein)
├── Show Membership Type (Ja/Nein)
├── Show Membership Begin (Ja/Nein)
├── Show First Membership (Ja/Nein)
├── Show Entry Year (Ja/Nein)
└── Show Exit Year (Ja/Nein)

Ordering:
├── Primary Order By (lastname, firstname, city, ...)
├── Order Direction (ASC/DESC)
└── Secondary Order By (optional)

Display:
└── Display Num (5, 10, 15, 20, 25, 30, 50, 100)
```

**Eintritte/Austritte:**
```
Movement Options:
├── Movement Type (entries/exits)
└── Movement Year (Jahr)

[+ alle Display Options wie "Aktive Mitglieder"]
```

---

## 👥 Benutzer-Rollen

### Administrator

**Rechte:**
- Vollzugriff auf alle Funktionen
- Personen anlegen/bearbeiten/löschen
- Mitgliedschaften verwalten
- Bankdaten verwalten
- DSGVO Cleanup durchführen
- Migration durchführen
- Stammdaten pflegen
- Komponenten-Konfiguration

**Typische Aufgaben:**
- Neue Mitglieder aufnehmen
- Mitgliedschaftswechsel durchführen
- DSGVO-Bereinigung
- Jahresabschlüsse
- Statistiken erstellen

### Manager

**Rechte:**
- Lesen und Bearbeiten
- Keine Lösch-Rechte
- Kein DSGVO Cleanup
- Keine Konfiguration

**Typische Aufgaben:**
- Daten aktualisieren
- Neue Mitglieder aufnehmen
- Reports erstellen

### Mitglied

**Rechte:**
- Nur eigene Daten lesen
- Frontend-Zugriff

**Typische Aufgaben:**
- Eigenes Profil ansehen
- Mitgliedschafts-Historie einsehen
- Kontaktdaten prüfen

### Öffentlich

**Rechte:**
- Nur öffentliche Listen lesen
- Kein Login erforderlich

**Typische Aufgaben:**
- Aktive Mitglieder einsehen
- Eintritte/Austritte einsehen

---

## 📊 Typische Workflows

### Workflow 1: Neues Mitglied aufnehmen

```
1. Backend → ClubOrganisation → Personen → New
2. Formular ausfüllen:
   ├── Anrede, Vor-/Nachname
   ├── Kontaktdaten
   ├── Mitgliedsnummer vergeben
   └── Optional: Foto hochladen
3. Save & Close

4. Backend → Mitgliedschaften → New
5. Person auswählen
6. Mitgliedschaftstyp wählen
7. Begin-Datum setzen (End-Datum leer lassen)
8. Save & Close

9. Optional: Bankverbindung anlegen
   ├── Verschlüsselungsschlüssel eingeben
   ├── IBAN, BIC, Kontoinhaber eingeben
   └── Save

Entry Year wird automatisch berechnet ✓
```

### Workflow 2: Mitgliedschaftswechsel

```
1. Backend → Mitgliedschaften → [Aktuelle Mitgliedschaft öffnen]
2. End-Datum setzen (z.B. 31.12.2025)
3. Save & Close

4. Backend → Mitgliedschaften → New
5. Dieselbe Person auswählen
6. Neuen Mitgliedschaftstyp wählen
7. Begin-Datum setzen (z.B. 01.01.2026)
8. Save & Close

Überschneidungsprüfung verhindert Fehler ✓
```

### Workflow 3: DSGVO Cleanup

```
1. Backend → ClubOrganisation → Options
2. DSGVO Jahre-Schwelle konfigurieren (z.B. 3 Jahre)
3. Save & Close

4. Backend → ClubOrganisation → DSGVO Cleanup
5. Liste zeigt Personen mit alten beendeten Mitgliedschaften
6. Prüfen: Keine aktiven Mitgliedschaften ✓
7. Person(en) auswählen
8. Button "Anonymisieren" klicken
9. Bestätigen

Daten werden anonymisiert ✓
Bankverbindungen gelöscht ✓
Active Flag = 0 ✓
```

### Workflow 4: Migration von Clubmanagement

```
1. Alte Clubmanagement-Datenbank exportieren
2. Backend → ClubOrganisation → Migration
3. Datenbank-Verbindung konfigurieren
4. Mapping prüfen (Felder zuordnen)
5. "Start Migration" klicken
6. Fortschritt beobachten
7. Fehlerprotokoll prüfen
8. Bei Erfolg: Commit
9. Bei Fehler: Rollback

Daten werden importiert ✓
Verknüpfungen erstellt ✓
```

---

## 📈 Anwendungsfälle

### Kleiner Sportverein (50 Mitglieder)

**Anforderungen:**
- Mitgliederverwaltung
- Beiträge per SEPA
- Öffentliche Mitgliederliste

**Setup:**
1. Personen anlegen (50)
2. Mitgliedschaften zuweisen
3. Bankverbindungen hinterlegen
4. Frontend-Menüpunkt "Aktive Mitglieder"

**Nutzung:**
- Admin pflegt Daten monatlich
- Mitglieder sehen öffentliche Liste
- SEPA-Dateien aus Bankdaten generieren (extern)
- Jährlich DSGVO Cleanup

### Großer Verband (500+ Mitglieder)

**Anforderungen:**
- Komplexe Mitgliedschaftsstrukturen
- Mehrere Beitragsklassen
- Historische Daten
- DSGVO-Compliance

**Setup:**
1. Mitgliedschaftstypen definieren (10+)
2. Massenimport via Migration Tool
3. ACL für mehrere Administratoren
4. Frontend nur für eingeloggte Mitglieder

**Nutzung:**
- Mehrere Admins verwalten Daten
- Automatische Entry/Exit Year
- Statistiken via SQL-Queries
- Quartalsweise DSGVO Cleanup

### Familienverein

**Anforderungen:**
- Familienmitgliedschaften
- Unterschiedliche Beiträge
- Kinder als Familienmitglieder

**Setup:**
1. Mitgliedschaftstypen:
   ├── Familienmitglied (zahlend)
   └── Familienmitglied
2. Personen anlegen (alle Familienmitglieder)
3. Mitgliedschaften zuweisen
4. Bankverbindung nur für zahlendes Mitglied

**Nutzung:**
- Familien als Gruppe verwalten
- Ein Beitrag für Familie
- Alle Mitglieder in Listen

---

## 🎨 Anpassungsmöglichkeiten

### Mitgliedschaftstypen erweitern

```sql
INSERT INTO #__cluborganisation_membershiptypes 
(title, description, ordering, state)
VALUES 
('Ehrenmitglied', 'Kostenlose Mitgliedschaft für verdiente Mitglieder', 5, 1),
('Fördermitglied', 'Passives Mitglied mit Förderbeitrag', 6, 1);
```

### Anreden erweitern

```sql
INSERT INTO #__cluborganisation_salutations 
(title, ordering, state)
VALUES 
('Prof.', 4, 1),
('Dr.', 5, 1);
```

### Custom Fields hinzufügen

1. SQL: Spalte hinzufügen
2. Table-Klasse: `getTableColumns()` aktualisieren
3. Form XML: Feld hinzufügen
4. Template: Feld anzeigen
5. Sprachdateien: Labels definieren

### Neue Frontend-View

1. Model erstellen (ListModel)
2. View erstellen (HtmlView.php)
3. Template erstellen (default.php)
4. Menu Item Type registrieren (.sys.ini)
5. Parameter definieren (default.xml)

---

## 🔍 Reporting & Statistiken

### SQL-Queries für Reports

**Mitgliederentwicklung:**
```sql
SELECT 
    entry_year,
    COUNT(*) as count
FROM #__cluborganisation_persons
WHERE entry_year IS NOT NULL
GROUP BY entry_year
ORDER BY entry_year;
```

**Aktuelle Mitgliederzahl:**
```sql
SELECT COUNT(DISTINCT p.id) as active_members
FROM #__cluborganisation_persons p
JOIN #__cluborganisation_memberships m ON p.id = m.person_id
WHERE m.begin <= CURDATE()
AND (m.end >= CURDATE() OR m.end IS NULL)
AND p.active = 1
AND p.deceased IS NULL;
```

**Altersstruktur:**
```sql
SELECT 
    CASE 
        WHEN YEAR(CURDATE()) - YEAR(birthday) < 18 THEN 'Unter 18'
        WHEN YEAR(CURDATE()) - YEAR(birthday) < 30 THEN '18-29'
        WHEN YEAR(CURDATE()) - YEAR(birthday) < 50 THEN '30-49'
        WHEN YEAR(CURDATE()) - YEAR(birthday) < 65 THEN '50-64'
        ELSE 'Über 65'
    END as age_group,
    COUNT(*) as count
FROM #__cluborganisation_persons p
JOIN #__cluborganisation_memberships m ON p.id = m.person_id
WHERE m.begin <= CURDATE()
AND (m.end >= CURDATE() OR m.end IS NULL)
AND p.active = 1
GROUP BY age_group
ORDER BY 
    CASE age_group
        WHEN 'Unter 18' THEN 1
        WHEN '18-29' THEN 2
        WHEN '30-49' THEN 3
        WHEN '50-64' THEN 4
        ELSE 5
    END;
```

**Mitgliedschaftstypen-Verteilung:**
```sql
SELECT 
    t.title,
    COUNT(*) as count
FROM #__cluborganisation_memberships m
JOIN #__cluborganisation_membershiptypes t ON m.type = t.id
WHERE m.begin <= CURDATE()
AND (m.end >= CURDATE() OR m.end IS NULL)
GROUP BY t.title
ORDER BY count DESC;
```

---

## 💡 Tipps & Tricks

### Performance-Optimierung

**Indizes prüfen:**
```sql
SHOW INDEX FROM #__cluborganisation_persons;
SHOW INDEX FROM #__cluborganisation_memberships;
```

**Query-Optimierung:**
- Subqueries für Entry/Exit Year sind effizient
- Active-Check via Subquery statt JOIN
- Prepared Statements verhindern SQL-Injection

### Datensicherung

**Regelmäßige Backups:**
```bash
# Datenbank-Backup
mysqldump -u root -p joomla_db \
    --tables \
    ypvlj_cluborganisation_persons \
    ypvlj_cluborganisation_memberships \
    ypvlj_cluborganisation_membershipbanks \
    ypvlj_cluborganisation_salutations \
    ypvlj_cluborganisation_membershiptypes \
    > cluborganisation_backup_$(date +%Y%m%d).sql

# Fotos sichern
tar -czf images_backup_$(date +%Y%m%d).tar.gz \
    /var/www/html/images/cluborganisation/
```

### DSGVO-Compliance

**Regelmäßig prüfen:**
- Quartalsweise DSGVO Cleanup durchführen
- Aufbewahrungsfristen dokumentieren
- Audit-Trail führen (modified_by, modified)
- Datenschutz-Dokumentation aktualisieren

### Benutzer-Schulung

**Administrator-Training:**
- Zeitraum-Überschneidungsprüfung erklären
- DSGVO Cleanup demonstrieren
- Verschlüsselung für Bankdaten zeigen
- ACL-Konzept vermitteln

**Mitglieder-Information:**
- Frontend-Zugriff erklären
- Datenschutz kommunizieren
- Self-Service-Möglichkeiten aufzeigen

---

**Stand:** Februar 2026  
**Version:** 1.0.0

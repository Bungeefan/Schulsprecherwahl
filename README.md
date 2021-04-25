# Schulsprecherwahl
Ein neues und modernes Schulsprecherwahlsystem mit Dark Theme.

Kompatibilität getestet mit: Chrome 77+, Firefox 69+  
**Nicht kompatibel mit IE** 

### Allgemeine Konfiguration
* **2 Möglichkeiten, um _config.inc.php_ zu erstellen:**
  * Rechte des Deployment Ordners anpassen, damit PHP die Konfiguration automatisch verwalten
    kann (`chmod -R 0777 Schulsprecherwahl/`)
  * _config.sample.inc.php_ zu _config.inc.php_ kopieren/umbenennen\
    (`cp config.sample.inc.php config.inc.php`)
* **Rechte der Ordner berichtigen**
  * Rechte des Upload Ordners anpassen, damit PHP die hochgeladenen Bilder verwalten kann\
    (`chmod -R 0777 Schulsprecherwahl/uploads`)
  * Rechte der Konfigurationsdatei anpassen, damit PHP die Einstellungen (letzter Tab) speichern und lesen kann. Es kann
    sein das PHP die Datei zuerst erstellen muss.\
    (`chmod 0777 Schulsprecherwahl/settings.cfg.json`)
* **Datenbank erstellen**
  * Um eine Datenbank generieren zu lassen, gehen Sie in das Adminpanel (`/admin`) -> Einstellungen -> "Datenbank
    zurücksetzen/neu erstellen"

### Absichern des Admin Panels
1. Überprüfung ihrer [_.htaccess_ Konfiguration](http://httpd.apache.org/docs/current/mod/core.html#allowoverride)
2. _.htaccess.sample_ in _.htaccess_ umbenennen (`mv admin/.htaccess.sample admin/.htaccess`)
3. [_.htpasswd_  erstellen und konfigurieren](https://httpd.apache.org/docs/current/programs/htpasswd.html) (z.B.: `htpasswd -c admin/.htpasswd admin`)
4. Stellen Sie sicher, dass der Pfad in _.htaccess_, der auf die _.htpasswd_ verweist, richtig ist. Gegebenenfalls korrigieren Sie ihn.

### Screenshots
#### Main
![Main](https://raw.githubusercontent.com/Bungeefan/Schulsprecherwahl/assets/screenshots/Main.png)
![Voting](https://raw.githubusercontent.com/Bungeefan/Schulsprecherwahl/assets/screenshots/Voting.png)
#### AdminPanel
![Adminpanel Candidates](https://raw.githubusercontent.com/Bungeefan/Schulsprecherwahl/assets/screenshots/Adminpanel/Candidates.png)
Further screenshots
* [Adminpanel Keys](https://raw.githubusercontent.com/Bungeefan/Schulsprecherwahl/assets/screenshots/Adminpanel/Keys.png)
* [Adminpanel Results](https://raw.githubusercontent.com/Bungeefan/Schulsprecherwahl/assets/screenshots/Adminpanel/Results.png)
* [Adminpanel Settings](https://raw.githubusercontent.com/Bungeefan/Schulsprecherwahl/assets/screenshots/Adminpanel/Settings.png)

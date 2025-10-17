; Script for Inno Setup
;
; NOTE TO USER:
; To compile this script, you need to download the portable (zip) versions of:
; 1. Nginx for Windows
; 2. PHP 7.2 (Non-Thread Safe) for Windows
; 3. MariaDB for Windows
;
; Create the following directory structure in the root of this project:
; /build/
;   - nginx/ (containing extracted nginx files)
;   - php/ (containing extracted php files)
;   - mariadb/ (containing extracted mariadb files)
;
; Then, you can compile this 'installer.iss' script using the Inno Setup Compiler.

[Setup]
AppName=WebTV Player
AppVersion=1.0
DefaultDirName={pf}\WebTV Player
DefaultGroupName=WebTV Player
UninstallDisplayIcon={app}\www\Favicon.ico
Compression=lzma
SolidCompression=yes
WizardStyle=modern
OutputDir=.\

[Files]
; Server components (placeholders, to be provided by the user)
Source: "build\nginx\*"; DestDir: "{app}\nginx"; Flags: recursesubdirs createallsubdirs
Source: "build\php\*"; DestDir: "{app}\php"; Flags: recursesubdirs createallsubdirs
Source: "build\mariadb\*"; DestDir: "{app}\mariadb"; Flags: recursesubdirs createallsubdirs

; Application source code
Source: "admin\*"; DestDir: "{app}\www\admin"; Flags: recursesubdirs createallsubdirs
Source: "assets\*"; DestDir: "{app}\www\assets"; Flags: recursesubdirs createallsubdirs
Source: "css\*"; DestDir: "{app}\www\css"; Flags: recursesubdirs createallsubdirs
Source: "fonts\*"; DestDir: "{app}\www\fonts"; Flags: recursesubdirs createallsubdirs
Source: "images\*"; DestDir: "{app}\www\images"; Flags: recursesubdirs createallsubdirs
Source: "img\*"; DestDir: "{app}\www\img"; Flags: recursesubdirs createallsubdirs
Source: "includes\*"; DestDir: "{app}\www\includes"; Flags: recursesubdirs createallsubdirs
Source: "js\*"; DestDir: "{app}\www\js"; Flags: recursesubdirs createallsubdirs
Source: "lib\*"; DestDir: "{app}\www\lib"; Flags: recursesubdirs createallsubdirs
Source: "mediafiles\*"; DestDir: "{app}\www\mediafiles"; Flags: recursesubdirs createallsubdirs
Source: "oops\*"; DestDir: "{app}\www\oops"; Flags: recursesubdirs createallsubdirs
Source: "svg\*"; DestDir: "{app}\www\svg"; Flags: recursesubdirs createallsubdirs
Source: "themes\*"; DestDir: "{app}\www\themes"; Flags: recursesubdirs createallsubdirs
Source: "*.php"; DestDir: "{app}\www"
Source: "Favicon.ico"; DestDir: "{app}\www"
Source: "text.txt"; DestDir: "{app}\www"

; Database setup script
Source: "setup_db.sql"; DestDir: "{tmp}"

[Icons]
Name: "{group}\WebTV Player"; Filename: "{app}\start.bat"
Name: "{group}\Uninstall WebTV Player"; Filename: "{uninstallexe}"
Name: "{commondesktop}\WebTV Player"; Filename: "{app}\start.bat"; Tasks: desktopicon

[Tasks]
Name: "desktopicon"; Description: "Create a &desktop icon"; GroupDescription: "Additional icons:"

[Run]
; Initialize MariaDB data directory. This is a critical step.
Filename: "{app}\mariadb\bin\mysql_install_db.exe"; Parameters: "--datadir=""{app}\mariadb\data"""; WorkingDir: "{app}\mariadb\bin"; Flags: runhidden

; Start MariaDB to run the setup script
Filename: "{app}\mariadb\bin\mysqld.exe"; Parameters: "--datadir=""{app}\mariadb\data"" --console"; WorkingDir: "{app}\mariadb\bin"; Flags: runhidden; AfterInstall: PostInstallDBSetup

[Code]
procedure PostInstallDBSetup();
var
  ResultCode: Integer;
begin
  // Execute the SQL script to create the database and user.
  // This runs after MariaDB has been started.
  Exec(ExpandConstant('{app}\mariadb\bin\mysql.exe'), ExpandConstant('-u root < "{tmp}\setup_db.sql"'), '', SW_HIDE, ewWaitUntilTerminated, ResultCode);

  // Stop the temporary MariaDB instance. The user will start it with start.bat
  Exec(ExpandConstant('{app}\mariadb\bin\mysqladmin.exe'), '-u root shutdown', '', SW_HIDE, ewWaitUntilTerminated, ResultCode);
end;

procedure CurStepChanged(CurStep: TSetupStep);
begin
  if CurStep = ssPostInstall then
  begin
    // Create a batch file to start the servers and open the application
    Dim Lines: TArrayOfString;
    SetArrayLength(Lines, 8);
    Lines[0] := '@echo off';
    Lines[1] := 'echo Starting WebTV Player servers...';
    Lines[2] := 'cd /d "%~dp0"';
    Lines[3] := 'start "Nginx" /B nginx\nginx.exe -p nginx';
    Lines[4] := 'start "MariaDB" /B mariadb\bin\mysqld.exe --datadir=mariadb\data';
    Lines[5] := 'echo Waiting for servers to initialize...';
    Lines[6] := 'timeout /t 5 /nobreak >nul';
    Lines[7] := 'start "" "http://localhost"';
    SaveStringsToFile(ExpandConstant('{app}\start.bat'), Lines, False);
  end;
end;
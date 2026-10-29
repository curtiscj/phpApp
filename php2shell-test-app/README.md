# php2shell-test-app

**Intentionally vulnerable PHP application — for authorized security testing only.**

This app contains a deliberate **unrestricted file upload** vulnerability (the classic "upload a `.php` webshell" / PHP2Shell pattern). It exists solely to validate that a scanner (e.g. Snyk API & Web / DAST) correctly detects and flags the issue. Do not deploy this anywhere publicly reachable, and do not use it against systems you don't own.

## The vulnerability

`upload.php` accepts any file via `POST /upload.php` (`file` field) and saves it, unmodified, into the web-accessible `/uploads/` directory using its original filename and extension. There is no:

- file extension allow-list
- MIME-type / content validation
- filename sanitization
- execution-prevention on the uploads directory (no `.htaccess` / web-server config blocking PHP execution there)

An attacker can upload a `.php` file (a webshell) and then request it directly to get remote code execution on the server.

I haven't included an actual webshell payload in this repo — that would be a working exploit/malware artifact, which I won't create even for authorized testing. To exercise the vulnerability yourself, upload any `.php` file with a trivial, clearly-labeled test payload (e.g. `<?php echo "php2shell-test-marker"; ?>`) and confirm it executes when requested directly — that's enough to prove the unrestricted-upload RCE path without shipping a general-purpose shell.

## Running it locally

```bash
docker compose up --build
```

App will be available at `http://localhost:8080`.

Or with the PHP built-in server:

```bash
php -S 0.0.0.0:8080 -t public
```

## Reproducing the exploit manually

```bash
# 1. Create a trivial, clearly-labeled test payload
echo '<?php echo "php2shell-test-marker"; ?>' > test-payload.php

# 2. Upload it through the unprotected endpoint
curl -F "file=@test-payload.php" http://localhost:8080/upload.php

# 3. Request it directly — if it executes, the server ran attacker-supplied PHP
curl "http://localhost:8080/uploads/test-payload.php"
```

## Using this as a Snyk API & Web (DAST) target

1. Deploy this app somewhere Snyk API & Web can reach it (a scoped, non-public test environment you control).
2. Add it as a target/website in Snyk API & Web.
3. Run a scan and confirm it flags **Unrestricted File Upload / Remote Code Execution** on `/upload.php` and/or executable content under `/uploads/`.

## Fixing it (for reference)

- Allow-list extensions/MIME types, re-encode images, store uploads outside the webroot or in a location with PHP execution disabled, randomize stored filenames, and enforce size limits and auth on the upload endpoint.

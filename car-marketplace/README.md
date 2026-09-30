# KeretaKu: multi-broker new car marketplace (sample)

A Carsome-style sample written in plain PHP 8. It has no framework, no build step and no dependencies.

- **Main marketplace** (`keretaku.my`) lists every broker's cars in one place. Buyers can search and filter them, then view each car's details and send an enquiry. Each lead is routed automatically to the broker selling that car.
- **Each broker gets a subdomain** (`toyota.keretaku.my`, `proton.keretaku.my`, …). A broker site is a full showroom website that shows only that broker's stock.
- **Each broker can have their own design**, at three levels:
  1. **Colours only.** Set `colors` in `config/brokers.php`. Honda does this with the default theme.
  2. **Theme stylesheet.** Add `public/themes/<name>.css`. Proton uses `sleek` (navy, gold and serif) and Perodua uses `friendly` (rounded and bright).
  3. **Custom templates.** Add `themes/<name>/home.php` (or `car.php`, `layout.php`, …) to change the whole layout. Toyota uses `showroom`, a dark cinematic home page with a model-by-model line-up. Any template a theme doesn't provide falls back to `themes/base/`.
- A broker can also use **their own domain** by setting `custom_domain`.

## Pages

| Page | Main site | Broker site |
|---|---|---|
| `/` | Hero with search, brands, featured cars, body types, how it works, broker showcase | Branded hero, trust points, the broker's line-up |
| `/cars` | Every broker's cars, with filters (brand, body type, fuel, price, keyword), sorting and pagination | Same page, showing only this broker's stock |
| `/car/{id}` | Photo gallery, key specs, colours, highlights, loan calculator, enquiry form, seller card, similar cars | Same page, showing only this broker's cars |
| `/loan-calculator` | Interactive flat-rate hire-purchase calculator | ✓ |
| `/brokers` | Broker directory and a "become a partner" call to action | none |
| `/about` | none | Showroom info, stats, directions, WhatsApp |

Enquiries (test drive, quote or loan check) are validated and CSRF-protected, then appended to `storage/leads.jsonl`. Each lead is tagged with the broker and whether it came from the marketplace or the broker's own site.

## Photos

Car photos are real, openly licensed photos from **Wikimedia Commons** (CC BY / CC BY-SA). The browser loads them directly by file name. Each detail page links to the source files, and the footer credits the photographers. If a photo can't load, the page shows a clean placeholder instead of a broken image.

For production, brokers upload their own stock photos. Put a path or URL in a car's `images` list (e.g. `/uploads/vios-front.jpg`) and it is used as-is.

## Run locally

```bash
cd car-marketplace
php -S localhost:8000 -t public public/index.php
```

Chrome, Edge and Firefox resolve `*.localhost` to your own machine, so no hosts-file changes are needed:

- http://localhost:8000 is the main marketplace
- http://toyota.localhost:8000 is the custom "showroom" templates
- http://proton.localhost:8000 is the "sleek" stylesheet
- http://perodua.localhost:8000 is the "friendly" stylesheet
- http://honda.localhost:8000 is the default theme with brand colours only

If your browser can't use `*.localhost` (Safari), use `http://localhost:8000/?broker=toyota`. This only works while `APP_DEBUG=1`.

## View it on your phone

Your phone and computer need to be on the same Wi-Fi. On Mac or Linux:

```bash
cd car-marketplace
./serve-phone.sh
```

The script prints addresses like `http://toyota.192.168.1.20.nip.io:8000`; type one into your phone's browser. [nip.io](https://nip.io) is a free DNS service that points `anything.<ip>.nip.io` at `<ip>`, so the broker subdomains work on a phone without setup.

On Windows, find your IP with `ipconfig` (the IPv4 address), then run:

```powershell
$env:BASE_DOMAIN="192.168.1.20.nip.io"; php -S 0.0.0.0:8000 -t public public/index.php
```

If the phone can't connect, allow PHP through your computer's firewall when it asks. Some home routers block nip.io names that point to local addresses; if yours does, deploy the site online (see below).

## How it works

```
Request Host header ──► resolve_site()  (src/bootstrap.php)
   keretaku.my            → main marketplace   (theme: marketplace, all brokers' cars)
   toyota.keretaku.my     → broker "toyota"    (theme from config, only their cars)
   www.my-own-domain.my   → broker with that custom_domain
   unknown.keretaku.my    → 404
```

| Path | What it is |
|---|---|
| `public/index.php` | Front controller and routes |
| `public/app.js` | Gallery, loan calculator, instant filters, mobile menu (optional; pages work without JS) |
| `src/bootstrap.php` | Tenant resolution, theme lookup, filtering, photos, icons |
| `config/brokers.php` | Broker registry: subdomain, contact, rating, theme, colours |
| `data/cars.php` | Inventory with specs, colours, features, promos and photos |
| `themes/base/` | Default templates; every theme falls back here |
| `themes/marketplace/`, `themes/showroom/` | Template overrides |
| `public/themes/*.css` | Design system (`base.css`) and per-theme styling |

## Adding a broker

1. Add an entry to `config/brokers.php`. The array key becomes the subdomain.
2. Add their cars to `data/cars.php` with `'broker' => '<key>'`.
3. Choose a theme: pick an existing one, add a stylesheet, or add template overrides.

## Deploying (production)

1. **DNS:** create a wildcard record, `*.keretaku.my  A  <server-ip>`, plus a record for `keretaku.my` itself.
2. **SSL:** get a wildcard certificate for `*.keretaku.my`, either from Let's Encrypt with a DNS challenge or through Cloudflare.
3. **Web server** (nginx example):

   ```nginx
   server {
       listen 443 ssl;
       server_name keretaku.my *.keretaku.my;   # add broker custom domains here too
       root /var/www/car-marketplace/public;
       index index.php;
       location / { try_files $uri /index.php?$query_string; }
       location ~ \.php$ {
           include fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root/index.php;
           fastcgi_param BASE_DOMAIN keretaku.my;
           fastcgi_param APP_DEBUG 0;
           fastcgi_pass unix:/run/php/php8.3-fpm.sock;
       }
   }
   ```

## Next steps toward a real product

- Move `brokers`, `cars` and `leads` into MySQL. The array shapes map directly to tables.
- Add a broker dashboard where each broker logs in to manage stock, upload photos, edit their theme colours and handle leads.
- Add a super-admin panel to onboard brokers, verify them and assign themes.
- Add car comparison, saved cars, a 360° or video gallery, and multiple variants per model.
- Send new leads to the broker's WhatsApp or email.

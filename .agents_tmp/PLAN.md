# Gamifikacijski PRO Vtičnik za VidMov - Načrt

## 1. OBJECTIVE
Razvoj lastnega gamifikacijskega vtičnika "VidGamify PRO", ki bo nadgradnja in izboljšava obstoječe MyCred integracije z dodanimi naprednimi funkcijami za motivacijo uporabnikov, interakcijo in monetizacijo vsebin.

**Glavni cilj:** Ustvariti celovit gamifikacijski sistem z več kot 30 novimi funkcijami, ki bodo nadgradile obstoječo MyCred integracijo v VidMov temi.

## 2. CONTEXT SUMMARY
Trenutna struktura projekta VidMov vključuje:
- **WordPress tema** z vgrajenim sistemom za video/audio vsebine
- **MyCred integracija** preko `beeteam368_mycred_hook.php` (video gledanje, reakcije)
- **Virtualna darila** (`virtual-gifts/virtual-gifts.php`) - prenos točk med uporabniki
- **Prodaja vsebin** (`mycred-sell-content/mycred-sell-content.php`) - pay-per-view
- **Membership sistemi** (`membership/membership.php`) - ArMember in Paid Membership Pro integracija
- **buyCred** (`buycred/buycred.php`) - nakup točk s Stripe/WooCommerce
- **Video kvizi** (`video-quizzes/video-quizzes.php`) - interaktivni kvizi z nagrajami
- **Trending sistem** (`trending/trending.php`) - priljubljene vsebine
- **History sistem** (`history/history.php`) - zgodovina gledanja

**Osnovne funkcije MyCred v projektu:**
1. Gledanje videov/avdia (točke za uporabnike in ustvarjalce)
2. Reakcije na objave (+/- točke)
3. Virtualna darila (prenos točk med uporabniki)
4. Prodaja vsebin (plačilo s točkami)
5. Nakup točk (buyCred)

## 3. APPROACH OVERVIEW
**Izbrani pristop:** Razvoj modularnega PRO vtičnika z naslednjimi ključnimi lastnostmi:

1. **Modularna arhitektura** - vsaka funkcija je ločen modul za lažjo vzdržljivost
2. **Napredna gamifikacija** - nivoji, dosežki, lestvice, nagrade
3. **Socialne funkcije** - sledilci, prijatelji, skupine
4. **Monetizacija** - več načinov zaslužka za ustvarjalce
5. **Analitika** - podrobni vpogledi v interakcije
6. **Integracije** - WooCommerce, Stripe, PayPal, email marketing

**Zakaj ta pristop:**
- Ohranja kompatibilnost z obstoječo MyCred integracijo
- Omogoča postopno nadgradnjo funkcij
- Modularnost omogoča lažje razširitve v prihodnosti
- Uporablja podobne vzorce koda kot obstoječi plugini

## 4. IMPLEMENTATION STEPS

### Faza 1: Osnovna struktura in konfiguracija (Teden 1)

#### Korak 1.1: Ustvaritev glavne datoteke vtičnika
**Cilj:** Ustrezno strukturiran PRO vtičnik z osnovnimi nastavitvami
**Metoda:** 
- Ustvari glavno datoteko `vidgamify-pro.php` z naslednjo strukturo:
  - Plugin header (ime, verzija, avtor)
  - Konstante za pot in URL
  - Preverjanje odvisnosti (MyCred, WordPress verzija)
  - Avtomatsko nalaganje modulov
- Ustvari mapo strukture: `/vidgamify-pro/inc/modules/`, `/assets/css/`, `/assets/js/`, `/languages/`

**Referenca:** `beeteam368-extensions-pro/inc/load-pro.php`

#### Korak 1.2: Nastavitvena stran in konfiguracija
**Cilj:** Centralizirana upravljaška stran za vse gamifikacijske nastavitve
**Metoda:**
- Ustvari admin nastavitveno stran z CMB2 frameworkom
- Dodaj naslednje nastavitvene sekcije:
  - Splošne nastavitve (ime valute, ikone, barve)
  - Točkovni sistemi (dodeljevanje točk za različne akcije)
  - Nivoji in rangi (nastavitev nivojev)
  - Dosežki in medalje (nastavitev dosežkov)
  - Lestvice in razvrstitve (globalne in lokalne lestvice)
  - Obvestila (email, obvestila v sistemu)

**Referenca:** `beeteam368-extensions-pro/inc/settings-pro/settings-pro.php`

#### Korak 1.3: Baza podatkov in shema
**Cilj:** Ustvaritev potrebnih tabel za nove funkcije
**Metoda:**
- Dodaj naslednje tabele preko `dbDelta()`:
  - `wp_vidgamify_levels` - podrobnosti o nivojih
  - `wp_vidgamify_achievements` - dosežki in medalje
  - `wp_vidgamify_leaderboards` - lestvice
  - `wp_vidgamify_user_levels` - nivoji uporabnikov
  - `wp_vidgamify_user_achievements` - dosežki uporabnikov
  - `wp_vidgamify_leaderboard_entries` - vnosi na lestvicah
  - `wp_vidgamify_badges` - medalje in odlikovanja
  - `wp_vidgamify_streaks` - zaporedna dneva aktivnosti

**Referenca:** WordPress `dbDelta()` funkcija, podobno kot v drugih pluginih

### Faza 2: Jedrne gamifikacijske funkcije (Teden 2-3)

#### Korak 2.1: Sistem nivojev in izkušenj
**Cilj:** Implementacija sistema XP (izkušnje) in nivojev za uporabnike
**Metoda:**
- Ustvari modul `class-vidgamify-levels.php`:
  - XP točke za vsako akcijo (gledanje, reakcije, objave...)
  - Nivoji od 1 do 100 z naraščajočimi zahtevami
  - Avtomatska nadgradnja nivoja ob dosegu cilja
  - Bonusi za višje nivoje (več točk za akcije)
  - Vizualni prikaz XP progress bar v profilu
- Dodaj kratke kode: `[vidgamify_user_level]`, `[vidgamify_xp_bar]`

**Referenca:** `beeteam368_mycred_video_viewing_class` (dodeljevanje točk)

#### Korak 2.2: Sistem dosežkov in medalj
**Cilj:** Ustvaritev sistema dosežkov za motivacijo uporabnikov
**Metoda:**
- Ustvari modul `class-vidgamify-achievements.php`:
  - Dosežki za različne akcije (prva objava, 100 ogledov, 50 reakcij...)
  - Medalje za dolgoročne cilje (30 dni zapored, 1000 točk...)
  - Skriti dosežki za skrivnostne nagrade
  - Prikaz dosežkov v profilu in na strani ustvarjalca
  - Obvestila ob pridobitvi novega dosežka
- Dodaj kratke kode: `[vidgamify_achievements]`, `[vidgamify_badges]`

**Referenca:** `beeteam368_mycred_author_reaction_plus_class` (akcijski dogodki)

#### Korak 2.3: Lestvice in razvrstitve
**Cilj:** Implementacija tekmovalnih lestvic za motivacijo
**Metoda:**
- Ustvari modul `class-vidgamify-leaderboards.php`:
  - Globalne lestvice (vsi uporabniki, vsi ustvarjalci)
  - Lokalno lestvice (po kategorijah, serijah, playlistah)
  - Časovne lestvice (dnevne, tedenske, mesečne)
  - Več kategorij (točke, nivoji, dosežki, aktivnost...)
  - Skrite lestvice za zasebne skupine
  - Avtomatsko posodabljanje vsakih 15 minut
- Dodaj kratke kode: `[vidgamify_leaderboard]`, `[vidgamify_ranking]`

**Referenca:** `beeteam368_trending_front_end` (določanje trendov)

#### Korak 2.4: Sistem zaporednih dni (Streaks)
**Cilj:** Motivacija za vsakodnevno aktivnost
**Metoda:**
- Ustvari modul `class-vidgamify-streaks.php`:
  - Štetje zaporednih dni aktivnosti
  - Nagrade za dolge streake (7, 14, 30 dni...)
  - Vizualni prikaz streaka v profilu
  - Opozorila pred prekinjanjem streaka
  - "Streak freeze" možnost za ohranitev streaka
- Dodaj kratke kode: `[vidgamify_streak]`, `[vidgamify_daily_reward]`

**Referenca:** `beeteam368_history_front_end` (shranjevanje zgodovine)

### Faza 3: Socialne funkcije (Teden 4-5)

#### Korak 3.1: Sistem sledilcev in prijateljev
**Cilj:** Povečanje socialne interakcije med uporabniki
**Metoda:**
- Ustvari modul `class-vidgamify-social.php`:
  - Sledenje uporabnikom (sledi, sledilci)
  - Prijatelji in zahteve za prijateljstvo
  - Obvestila o novih sledilcih/prijateljih
  - Točke za pridobivanje sledilcev (omejeno dnevno)
  - Dosežki za število sledilcev/skupin
- Dodaj kratke kode: `[vidgamify_followers]`, `[vidgamify_friends]`

**Referenca:** `beeteam368_virtual_gifts_front_end` (interakcija med uporabniki)

#### Korak 3.2: Skupine in klubi
**Cilj:** Ustvarjanje skupnostnih struktur
**Metoda:**
- Ustvari modul `class-vidgamify-groups.php`:
  - Ustvarjanje skupin/klubov s strani uporabnikov
  - Članarine v točkah za dostop do ekskluzivne vsebine
  - Skupinske lestvice in tekmovanja
  - Skupinski dosežki
  - Skupinska darila in nagrade
- Dodaj kratke kode: `[vidgamify_groups]`, `[vidgamify_club]`

**Referenca:** `beeteam368_membership` (članarine in dostop)

#### Korak 3.3: Socialne reakcije in interakcije
**Cilj:** Razširitev sistema reakcij z več možnostmi
**Metoda:**
- Ustvari modul `class-vidgamify-reactions.php`:
  - Več vrst reakcij (lp, love, wow, sad, cool...)
  - Točke za vsako vrsto reakcije
  - Dosežki za prejem različnih reakcij
  - Lestvice po tipu reakcij
  - Povprečne reakcije na profilu ustvarjalca
- Dodaj kratke kode: `[vidgamify_reactions]`, `[vidgamify_popularity]`

**Referenca:** `beeteam368_mycred_author_reaction_plus_class` (reakcije)

### Faza 4: Monetizacija in nagrade (Teden 6-7)

#### Korak 4.1: Napredni sistemi prodaje
**Cilj:** Več načinov monetizacije za ustvarjalce
**Metoda:**
- Ustvari modul `class-vidgamify-monetization.php`:
  - Pay-per-view z MyCred točkami (podpora obstoječi funkciji)
  - Pretplata s točkami (mesečna/letna)
  - Ekskluzivna vsebina za določene nivoje
  - Napovedane objave za prednaročilo s točkami
  - Bundle ponudbe (več vsebin po ugodnejši ceni)
- Dodaj kratke kode: `[vidgamify_premium_content]`, `[vidgamify_subscription]`

**Referenca:** `beeteam368_myCred_sell_content_front_end` (prodaja vsebin)

#### Korak 4.2: Sistem nagrad in kuponov
**Cilj:** Omogočanje ustvarjalcem ponudbo nagrad
**Metoda:**
- Ustvari modul `class-vidgamify-rewards.php`:
  - Kupon za popuste (točke za nakup točk)
  - Nagrade za ustvarjalce (denarna povračila v točkah)
  - Loterija z točkami (redne nagradne igre)
  - Dnevne nagradne igre
  - Ekskluzivne ponudbe za visoke nivoje
- Dodaj kratke kode: `[vidgamify_rewards]`, `[vidgamify_coupons]`

**Referenca:** `beeteam368_buyCred_front_end` (nakup točk)

#### Korak 4.3: Virtualna tržnica
**Cilj:** Trgovina z virtualnimi dobrinami
**Metoda:**
- Ustvari modul `class-vidgamify-marketplace.php`:
  - Nakup virtualnih dobrin (okviri, ikone, efekti)
  - Prodaja lastnih dobrin ustvarjalcem
  - Omejene izdaje za ekskluzivnost
  - Tržniški sistem s provizijo
  - Pregled prodaj in dobičkov
- Dodaj kratke kode: `[vidgamify_marketplace]`, `[vidgamify_shop]`

**Referenca:** `beeteam368_virtual_gifts_front_end` (virtualna darila)

### Faza 5: Analitika in poročanje (Teden 8)

#### Korak 5.1: Upraviteljska analitika
**Cilj:** Podrobni vpogledi v gamifikacijski sistem
**Metoda:**
- Ustvari modul `class-vidgamify-analytics.php`:
  - Pregled točk in transakcij
  - Aktivnost uporabnikov (dnevno, tedensko, mesečno)
  - Najbolj aktivni uporabniki in ustvarjalci
  - Prihodki od prodaje vsebin
  - Trendi rasti sistema
- Dodaj grafe in vizualizacije v admin panel

**Referenca:** `beeteam368_myCred_sell_content_front_end` (sales count)

#### Korak 5.2: Porocila za ustvarjalce
**Cilj:** Pomoč ustvarjalcem pri spremljanju uspeha
**Metoda:**
- Ustvari modul `class-vidgamify-creator-stats.php`:
  - Pregled točk prejetih od gledalcev
  - Analiza interakcij (reakcije, komentarji...)
  - Prihodki od prodaje vsebin
  - Rast sledilcev in aktivnosti
  - Primerjava s povprečjem v kategoriji
- Dodaj kratke kode: `[vidgamify_creator_stats]`

**Referenca:** `beeteam368_trending_front_end` (trending posts)

#### Korak 5.3: Porocila za uporabnike
**Cilj:** Osebni vpogledi v napredek uporabnikov
**Metoda:**
- Ustvari modul `class-vidgamify-user-stats.php`:
  - Pregled XP in nivoja
  - Dosežki in medalje
  - Aktivnost in streaki
  - Prihranki in poraba točk
  - Napredek na lestvicah
- Dodaj kratke kode: `[vidgamify_user_stats]`, `[vidgamify_progress]`

**Referenca:** `beeteam368_history_front_end` (zgodovina)

### Faza 6: Integracije in dodatki (Teden 9-10)

#### Korak 6.1: WooCommerce integracija
**Cilj:** Povezava z e-trgovino
**Metoda:**
- Ustvari modul `class-vidgamify-woocommerce.php`:
  - Nakup točk preko WooCommerce
  - Popusti za članove po nivojih
  - Nagrade za nakupe (točke nazaj)
  - Produkti z ekskluzivnim dostopom
  - Ponavljajoči se naročnini s točkami
- Integracija z obstoječim WooCommerce modulom

**Referenca:** `beeteam368_autoload_pro` (WooCommerce preverjanje)

#### Korak 6.2: Email in obvestila
**Cilj:** Komunikacija z uporabniki
**Metoda:**
- Ustvari modul `class-vidgamify-notifications.php`:
  - Email obvestila za dosežke in nagrade
  - Dnevna/tedenska poročila
  - Opomniki za streake
  - Obvestila o novih sledilcih
  - Integracija z WP email sistemi
- Dodaj nastavitve za vsako vrsto obvestila

**Referenca:** `beeteam368_notification/notification.php` (obvestila)

#### Korak 6.3: Email marketing integracije
**Cilj:** Povezava z email marketing orodji
**Metoda:**
- Ustvari modul `class-vidgamify-email-marketing.php`:
  - Integracija z Mailchimp, SendGrid...
  - Segmentacija po nivojih in aktivnosti
  - Avtomatska obvestila glede na akcije
  - A/B testiranje kampanj
  - Pregled učinkovitosti kampanj

**Referenca:** `beeteam368_fetch_data/fetch-data.php` (dodatne funkcije)

### Faza 7: Front-end komponente in UI (Teden 11-12)

#### Korak 7.1: Profilne strani in widgeti
**Cilj:** Izboljšan prikaz gamifikacijskih elementov
**Metoda:**
- Ustvari modul `class-vidgamify-widgets.php`:
  - Widget za XP bar v profilu
  - Widget za dosežke in medalje
  - Widget za lestvice v stranski vrsti
  - Widget za streake in dnevne nagrade
  - Widget za socialne metrike (sledilci, prijatelji)
- Prilagodi obstoječe template datoteke

**Referenca:** `beeteam368_virtual_gifts_front_end` (front-end prikaz)

#### Korak 7.2: Admin panel in nastavitve
**Cilj:** Učinkovito upravljanje sistema
**Metoda:**
- Ustvari modul `class-vidgamify-admin.php`:
  - Pregled vseh uporabnikov in njihovih nivojev
  - Upravljanje dosežkov in medalj
  - Urejanje lestvic in kategorij
  - Pregled transakcij in točk
  - Uvoz/izvoz podatkov
- Dodaj masovne akcije za upravljanje

**Referenca:** `beeteam368_mycred_hooks` (upravljanje hookov)

#### Korak 7.3: Responsive dizajn in optimizacija
**Cilj:** Optimiziran prikaz na vseh napravah
**Metoda:**
- Ustvari CSS datoteke za vsako komponento
- Dodaj responsive design za mobilne naprave
- Optimizacija hitrosti nalaganja
- Lazy loading za slike in grafe
- Minifikacija JS in CSS datotek

**Referenca:** `beeteam368_virtual_gifts_front_end::css()` (CSS datoteke)

### Faza 8: Testiranje in dokumentacija (Teden 13)

#### Korak 8.1: Celovito testiranje
**Cilj:** Zagotovitev brezhibnega delovanja
**Metoda:**
- Unit testi za vsak modul
- Integracijski testi med moduli
- Testiranje z različnimi uporabniškimi vlogami
- Testiranje obremenitve (veliko hkrati)
- Preverjanje kompatibilnosti s temi in drugimi plugini

**Referenca:** `beeteam368_mycred_video_viewing_class::run()` (testiranje hookov)

#### Korak 8.2: Dokumentacija
**Cilj:** Podrobna dokumentacija za uporabnike in developarje
**Metoda:**
- Ustvari README.md z osnovnimi informacijami
- API dokumentacija za developarje
- Uporabniški vodnik s primeri
- Video tutoriji za ključne funkcije
- FAQ sekcija za pogosta vprašanja

#### Korak 8.3: Poliranje in optimizacija
**Cilj:** Končno izboljšanje uporabniške izkušnje
**Metoda:**
- Popravljanje najdenih napak
- Optimizacija koda za hitrost
- Dodajanje tooltipov in pomoči
- Izboljšanje uporabniškega vmesnika
- Finalno testiranje

## 5. TESTING AND VALIDATION

### Kriteria uspeha:

1. **Funkcionalnost:**
   - Vsi moduli delujejo brez napak
   - Integracija z obstoječim MyCred sistemom
   - Avtomatsko dodeljevanje točk za vse akcije
   - Pravilno posodabljanje nivojev in dosežkov

2. **Performanse:**
   - Čas nalaganja strani < 3 sekunde
   - Minimalna poraba pomnilnika
   - Optimizirani SQL poizvedbi
   - Učinkovito shranjevanje v cache

3. **Kompatibilnost:**
   - Deluje z WordPress 5.8+
   - Kompatibilen z MyCred 1.7+
   - Prilagodljiv različnim temam
   - Brez konfliktov z drugimi plugini

4. **Uporabniška izkušnja:**
   - Intuitivna upravljaška stran
   - Responsive dizajn za vse naprave
   - Jasne vizualne povratne informacije
   - Hitro in učinkovito obveščanje

### Testni scenariji:

1. **Test dodeljevanja točk:**
   - Uporabnik gleda video → prejme točke
   - Uporabnik reagira na objavo → prejme/odšteje točke
   - Preverjanje pravilnega štetja in shranjevanja

2. **Test nivojev:**
   - Nabor XP točk → avtomatska nadgradnja nivoja
   - Preverjanje bonusov za višje nivoje
   - Vizualni prikaz progress bar

3. **Test dosežkov:**
   - Izpolnitev pogojev za dosežek → prejme medaljo
   - Obvestilo o novem dosežku
   - Prikaz v profilu

4. **Test lestvic:**
   - Posodabljanje lestvice vsakih 15 minut
   - Pravilna razvrstitev uporabnikov
   - Vizualni prikaz na strani

5. **Test monetizacije:**
   - Nakup vsebine s točkami → dostop do vsebine
   - Pretplata z mesečnimi točkami
   - Dostop do ekskluzivne vsebine

6. **Test socialnih funkcij:**
   - Sledenje uporabniku → obvestilo sledenemu
   - Prijateljstvo zahteva → sprejem/zavrnitev
   - Skupine in člani

### Validacija z uporabo:

1. **Ustvari testno okolje** z WordPress + VidMov tema
2. **Namesti obstoječi MyCred plugin** in konfiguriraj
3. **Namesti VidGamify PRO** in aktiviraj module
4. **Preveri vsako funkcijo** po korakih iz implementacije
5. **Zberi povratne informacije** od testnih uporabnikov
6. **Popravi napake** in optimiziraj kodo
7. **Pripravi končno verzijo** za produkcijo

### Merila za zaključek:

- [ ] Vsi 30+ novih funkcij deluje pravilno
- [ ] Integracija z MyCred brez težav
- [ ] Dokumentacija popolna in ažurna
- [ ] Testni scenariji uspešno opravljeni
- [ ] Performanse v mejah specifikacij
- [ ] Uporabniška izkušnja pozitivna
- [ ] Brez kritičnih napak (bugs)

---

**Skupno trajanje:** 13 tednov (približno 3 mesece)
**Pričakovana ekipa:** 2 developarja, 1 tester, 1 designer
**Ocena kompleksnosti:** Visoka - zahteva podrobno načrtovanje in testiranje


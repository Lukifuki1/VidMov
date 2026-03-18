# VidGamify PRO - Gamifikacijski Vtičnik za VidMov

## Pregled

VidGamify PRO je celovit gamifikacijski sistem, ki nadgrajuje obstoječo MyCred integracijo v VidMov temi z naprednimi funkcijami za motivacijo uporabnikov, interakcijo in monetizacijo vsebin.

## Ustvarjena Struktura

```
beeteam368-extensions-pro/
├── vidgamify-pro.php                    # Glavna datoteka vtičnika
├── inc/
│   ├── class-vidgamify-pro.php          # Glavni razred in inicializacija
│   └── modules/
│       ├── class-vidgamify-levels.php        # Sistem nivojev in XP
│       ├── class-vidgamify-achievements.php  # Dosežki in medalje
│       ├── class-vidgamify-leaderboards.php  # Lestvice in razvrstitve
│       ├── class-vidgamify-streaks.php       # Sistem zaporednih dni
│       ├── class-vidgamify-social.php        # Socialne funkcije (sledilci, prijatelji)
│       ├── class-vidgamify-groups.php        # Skupine in klubi
│       ├── class-vidgamify-reactions.php     # Razširjene reakcije
│       ├── class-vidgamify-membership.php    # Membership sistemi
│       ├── class-vidgamify-analytics.php     # Analitika in poročanje
│       ├── class-vidgamify-creator-stats.php # Statistik za ustvarjalce
│       ├── class-vidgamify-user-stats.php    # Osebne statistike uporabnikov
│       ├── class-vidgamify-woocommerce.php   # WooCommerce integracija
│       ├── class-vidgamify-notifications.php # Obvestila
│       ├── class-vidgamify-email-marketing.php # Email marketing
│       ├── class-vidgamify-widgets.php       # Frontend widgeti
│       └── class-vidgamify-admin.php         # Admin panel
├── assets/
│   ├── css/
│   │   ├── admin.css           # Admin stilski
│   │   └── frontend.css        # Frontend stilski
│   └── js/
│       ├── admin.js            # Admin skripte
│       └── frontend.js         # Frontend skripte
└── languages/                  # Translation files
```

## Ključne Funkcije

### 1. Sistem Nivojev in Izkušenj (XP)
- Avtomatsko dodeljevanje XP točk za različne akcije
- Nadgradnja nivojev z naraščajočimi zahtevami
- Bonusi za višje nivoje (več točk za akcije)
- Vizualni prikaz XP progress bar

### 2. Sistem Dosežkov in Medalj
- Več kot 30 prednastavljenih dosežkov
- Medalje za dolgoročne cilje
- Skriti dosežki za skrivnostne nagrade
- Prikaz v profilu in na strani ustvarjalca

### 3. Lestvice in Razvrstitve
- Globalne lestvice (vsi uporabniki, vsi ustvarjalci)
- Časovne lestvice (dnevne, tedenske, mesečne)
- Več kategorij (točke, nivoji, dosežki, aktivnost)

### 4. Sistem Zaporednih Dni (Streaks)
- Štetje zaporednih dni aktivnosti
- Nagrade za dolge streake (7, 14, 30 dni...)
- "Streak freeze" možnost za ohranitev streaka

### 5. Socialne Funkcije
- Sledenje uporabnikom in prijatelji
- Skupine in klubi z članarino v točkah
- Razširjeni sistem reakcij (like, love, wow, sad...)

### 6. Membership Sistemi
- 4 ravni članstva: Bronze, Silver, Gold, Platinum
- Ekskluzivne prednosti za vsako raven
- Popusti na WooCommerce nakupe

### 7. Analitika in Poročanje
- Upraviteljska analitika z grafi
- Statistik za ustvarjalce (views, engagement...)
- Osebni vpogledi v napredek uporabnikov

### 8. Integracije
- WooCommerce (nakupi točk, popusti)
- Email marketing (Mailchimp integracija)
- MyCred (avtomatsko dodeljevanje točk)

## Kratke Kode (Shortcodes)

| Koda | Opis |
|------|------|
| `[vidgamify_user_level]` | Prikaz uporabnikovega nivoja |
| `[vidgamify_xp_bar]` | XP progress bar |
| `[vidgamify_achievements]` | Uporabnikovi dosežki |
| `[vidgamify_badges]` | Seznam vseh medalj |
| `[vidgamify_leaderboard]` | Lestvica uporabnikov |
| `[vidgamify_ranking]` | Uporabnikova razvrstitev |
| `[vidgamify_streak]` | Trenutni streak |
| `[vidgamify_daily_reward]` | Dnevne nagrade |
| `[vidgamify_followers]` | Število sledilcev |
| `[vidgamify_friends]` | Število prijateljev |
| `[vidgamify_membership_tier]` | Membership raven |
| `[vidgamify_creator_stats]` | Statistik ustvarjalca |
| `[vidgamify_user_stats]` | Osebni statistik |

## Widgeti

1. **XP Progress Widget** - Prikaz XP progress bar v profilu
2. **Recent Achievements Widget** - Nedavni dosežki uporabnika
3. **Top Users Widget** - Lestvica najboljših uporabnikov
4. **Your Streak Widget** - Trenutni streak
5. **Social Stats Widget** - Socialne metrike

## Baze Podatkov

Ustvarjene tabele:
- `wp_vidgamify_levels` - Podrobnosti o nivojih
- `wp_vidgamify_achievements` - Dosežki in medalje
- `wp_vidgamify_user_levels` - Nivoji uporabnikov
- `wp_vidgamify_user_achievements` - Dosežki uporabnikov
- `wp_vidgamify_leaderboards` - Lestvice
- `wp_vidgamify_leaderboard_entries` - Vnosi na lestvicah
- `wp_vidgamify_streaks` - Zaporedni dnevi aktivnosti
- `wp_vidgamify_badges` - Medalje in odlikovanja
- `wp_vidgamify_user_badges` - Medalje uporabnikov
- `wp_vidgamify_groups` - Skupine in klubi
- `wp_vidgamify_group_members` - Članstvo v skupinah

## Integracija z Obstojčim MyCred Sistemom

VidGamify PRO se integrira z obstoječimi MyCred hooki:
- `beeteam368_mycred_video_viewing_class` - Gledanje videov
- `beeteam368_mycred_author_reaction_plus_class` - Reakcije na objave
- `beeteam368_virtual_gifts_front_end` - Virtualna darila

## Naprej

Za nadaljnji razvoj priporočamo:
1. Dodati več privzetih dosežkov in medalj
2. Implementirati naprednejšo analitiko z grafi
3. Dodati podporo za več jezikov (translation files)
4. Optimizirati SQL poizvedbe za večjo hitrost
5. Dodati testne scenarije za vsako funkcijo

## Avtorji

- **BeeTeam368** - Glavni razvijalec
- **VidMov Team** - Integracija z VidMov temo

---

*Verzija: 1.0.0*  
*Licenca: Themeforest Licence*

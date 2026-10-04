# Open Desk Network

A WordPress subdomain multisite. Each journalist runs their own site at
`<name>.opendesknetwork.com` as its **Publisher**. Network admins (super admins)
run everything else.

This repo holds only the custom code. It maps onto `wp-content/`:

```
wp-content/
  mu-plugins/odn-network.php      network rules (always on, can't be disabled)
  mu-plugins/odn-network/         roles, new-site defaults, blocks, WP-CLI
  themes/odn/                     the block theme
.deployignore                     server paths Pressable deploys must leave alone
bin/link-local.sh                 links this repo into the Local site
```

WordPress core, plugins and uploads are not in git.

## Who can do what

| | Publisher (own site) | Publisher (other sites) | Super admin |
|---|---|---|---|
| Write, publish, media, categories, comments | ✓ | – | ✓ |
| Site settings (title, tagline, reading, permalinks…) | ✓ | – | ✓ |
| Site Editor: templates, header/footer layouts, styles, navigation | ✓ | – | ✓ |
| Additional CSS (applies to their site only) | ✓ | – | ✓ |
| Install, activate or edit plugins and themes; switch theme | – | – | ✓ |
| Add or manage users, delete the site | – | – | ✓ |
| Unfiltered HTML / scripts in posts | – | – | ✓ |

The role is defined in `mu-plugins/odn-network/roles.php`. To change it, edit
`odn_publisher_capabilities()` and bump `ODN_ROLES_VERSION`. Every site picks up
the new definition on its next page load (or run `wp odn publisher sync-roles`).

Network-activated plugins that use `manage_options` for their settings pages
will show those pages to publishers on their own sites.

## Publishers can customise (per subdomain)

From **Appearance → Editor**:

- **Header layouts:** Classic, Banner (torn yellow edge), Desk Green, Image.
  Select the header, then **Replace** to swap layouts.
- **Footer layouts:** Desk Green, Paper, Banner.
- **Header image:** in the Image header, select the image → **Replace**.
- **Style variations:** Default (yellow), Press Red, Ink (dark), Newsroom Grey.
- **Additional CSS:** Styles → ⋮ → Additional CSS. Saved in that site only.
- **Block styles:** Torn edge (group, cover), Wide caps (text, titles),
  Thick rule (separator), Framed (image).

## Adding a journalist

```bash
wp odn publisher create janedoe jane@example.com --name="Jane Doe" --send-email
```

This creates the user (or reuses one with that email) and the site
`janedoe.<network domain>` on the `odn` theme, with them as its Publisher.
Sites added from **Network Admin → Sites → Add New** get the same treatment.

On Pressable, every subdomain must also be added as a domain on the site (and
in DNS), since there's no wildcard.

Other commands: `wp odn publisher list`, `wp odn publisher sync-roles`,
`wp odn setup main-site` (idempotent main-site setup: logo, home page, header).

## Local development

1. Local site `opendesknetwork` (`opendesknetwork.test`, subdomain multisite).
2. `bin/link-local.sh` symlinks the theme and mu-plugin into it.
3. Local doesn't add multisite subdomains to `/etc/hosts`. Add each one yourself:

   ```bash
   sudo sh -c 'printf "127.0.0.1 journalist1.opendesknetwork.test\n127.0.0.1 journalist2.opendesknetwork.test\n" >> /etc/hosts'
   ```

## Deploying

Pressable deploys `wp-content/` from this repo on push (GitHub integration). A
deploy **deletes** anything in the server's `wp-content` that isn't in the repo,
except uploads, Pressable's managed files and what `.deployignore` protects.
Plugins installed from the dashboard live in `wp-content/plugins/`, which
`.deployignore` protects.

Paths in `.deployignore` are neither deployed nor deleted, so never list a path
the repo ships.

# Deacon for Statamic

[Deacon](https://heydeacon.com) is an AI support agent that answers questions from your own docs and content, and says when it doesn't know. This addon adds its chat widget to every page of a Statamic site.

You need a Deacon account, and there's a free plan.

## Install

```sh
composer require heydeacon/statamic-deacon
```

## Set it up

Put the tag in the `<head>` of your layout, such as `resources/views/layout.antlers.html`:

```antlers
<head>
    {{# ...the rest of your head #}}
    {{ deacon }}
</head>
```

In a Blade layout, the same tag is `<s:deacon />`.

Then give it your key, in `.env`:

```sh
DEACON_KEY=pk_your_widget_key
```

To find your key, open Install from the button at the top of the Deacon dashboard. The snippet there has it in `data-key`, and it starts with `pk_`. It isn't a secret: every visitor's browser reads it. If the key is missing, the tag prints nothing and logs a warning that says why, so your pages still render. An option of the wrong type throws an `InvalidArgumentException` that says why.

Deacon only runs on the sites listed on Install. A listed domain covers its subdomains, so `example.com` covers `docs.example.com`. If your site lives somewhere else, such as `example.netlify.app`, add that address on Install too.

When you run the site on your own machine, at `example.test` with Herd or Valet, or on `localhost`, Install notices the widget there and lets you add it in one click. Chats on a development address don't count towards your plan.

If your site passes Deacon the details of whoever is logged in, with the `window.deaconUser` script from [Deacon's install guide](https://heydeacon.com/agent/install.md), put that script above `{{ deacon }}` in the head, because the widget reads it as it starts.

## Options

Publish the config file to change them:

```sh
php artisan vendor:publish --tag=deacon-config
```

That writes `config/deacon.php`:

| Option       | Type    | Default      | What it does |
| ------------ | ------- | ------------ | ------------ |
| `key`        | string  | `DEACON_KEY` | Your Deacon key. |
| `context`    | array   | none         | Details Deacon takes into account when it answers, the same on every page, such as `['product' => 'api', 'docs_version' => '3.2']`. Up to 12 keys, each value a string, number or boolean. Anyone can read them in the page's HTML, so never put a secret here. |
| `page_views` | boolean | `true`       | The same script counts visits for Deacon's website analytics. Set this to `false` to send no page views or JavaScript errors from your site. Chat still works. |

```php
return [
    'key' => env('DEACON_KEY'),
    'context' => ['product' => 'api', 'docs_version' => '3.2'],
    'page_views' => false,
];
```

## Content Security Policy

If your site gives each request a nonce, the tag carries it. It reads the nonce from Laravel's `Vite::useCspNonce()`, or from [spatie/laravel-csp](https://github.com/spatie/laravel-csp), which binds it as `csp-nonce`.

The policy also needs Deacon's address, `https://heydeacon.com`, in `script-src`, `connect-src` and `frame-src`, and `'unsafe-inline'` in `style-src`, because the widget writes its styles where a nonce can't reach. Keep the nonce out of `style-src`, since a nonce there makes browsers ignore `'unsafe-inline'`:

```text
script-src 'self' 'nonce-…' https://heydeacon.com;
style-src 'self' 'unsafe-inline';
connect-src 'self' https://heydeacon.com;
frame-src https://heydeacon.com
```

## Opening the chat from your own button

The widget comes with its own button. To use one of yours instead, hide Deacon's in Appearance in the dashboard and call `window.Deacon.open()` from your button or link. `Deacon.close()` and `Deacon.toggle()` work too.

## What it adds

The tag prints the same tag Install gives you:

```html
<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_your_widget_key" async></script>
```

It loads in the background without holding up the page. Works with Statamic 6, on PHP 8.2 and later.

## License

MIT

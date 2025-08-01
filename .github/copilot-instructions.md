---
applyTo: '**/*.php,**/*.js,**/*.scss'
---

# 🔧 Project Context
You are working on a **custom WordPress theme** named `alkooutlet`, built for WordPress 6.x and WooCommerce 8.x+. The stack includes PHP 8.x, SCSS (using BEM), and JavaScript. SwiperJS 11.2.10 is used.

# 🎯 General Goals
- Use **WordPress best practices**, clean architecture, and maintainable modular code.
- Follow **WordPress Coding Standards** for PHP and HTML.
- Optimize for **Core Web Vitals** and **PageSpeed**, using **mobile-first** design principles.
- Structure SCSS and templates cleanly, promoting reuse and avoiding code duplication.

# 🖌️ SCSS Guidelines
- Follow **BEM naming conventions** (e.g., `.block__element--modifier`).
- Use **mobile-first** media queries, defined as mixins in `_mixins.scss`.
- Always **nest SCSS properly** using `&`.
- Use `@use` or `@import` as needed to include partials (e.g., `_mixins.scss`, `_variables.scss`).
- Always use **variables** from `_variables.scss` for `color`, `padding`, `margin`, `gap`, etc.
- Use **mixins** from `_mixins.scss` for repetitive patterns or responsiveness.
- Layouts must use **flexbox**, but avoid brittle values like `flex: 0 0 30%`.
- Each SCSS component must live in its own logically named file.
- Swiper requires fixed `width` or `%`, not `flex-grow`.
- Use `.button`, `.button--primary`, `.button--outline` for buttons.

# 🧱 Layout & HTML
- Use semantic tags: `<header>`, `<main>`, `<section>`, `<nav>`, etc.
- Respect logical heading order (H1–H6).
- Add `aria-*`, `role`, `tabindex`, `lang`, `alt`, `aria-hidden` where necessary.
- `.container` class handles only `max-width: 1200px; margin-inline: auto; width: 100%`.
- When making section include main wrapper and inside .container div.


# 🐘 PHP / WordPress
- Use modern PHP syntax compatible with WP 6.x and PHP 8.x.
- Use `<?php // comment ?>` for dev-only notes. Never use `<!-- HTML comments -->`.
- Use PHPDoc for all functions.
- Do **not** duplicate HTML of reusable components (e.g., use `wc_get_template_part( 'content', 'product' )`).
- Never generate ACF code — assume ACF fields and IDs are pre-defined.
- When overriding WooCommerce templates, **retain original file version number** and path in comments.
- Break large page templates into reusable partials inside the `template-parts` folder.
- If unsure about WooCommerce template structure, request a copy before editing.

# 🛒 WooCommerce Integration
- Use only **native WooCommerce hooks and functions**.
- Reuse `content-product.php` for loops, sliders, or archive views.
- Prefer styling components over overriding full template files.
- Preserve WooCommerce directory and hierarchy.
- Ensure compatibility with WooCommerce addons.
- Avoid deprecated or outdated WooCommerce template warnings.

# 💼 Workflow & Collaboration
- Deliver **clean, production-ready code only**.
- Do **not** add explanations or summaries — only the code + where/how to apply it.
- Use includes and template parts for reused elements.
- Do **not guess** data structures — ask for field names or data formats.
- Propose performance and security optimizations where applicable.
- Maintain consistency and clarity in file structure.
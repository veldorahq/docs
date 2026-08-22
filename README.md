<div align="center">

# ▲ Veldora Documentation Website

**Official documentation and interactive UI showcase for the Veldora PHP Framework.**

[![Website](https://img.shields.io/badge/Live-veldora.modrao.com-10B981?style=flat-square)](https://veldora.modrao.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-8b5cf6?style=flat-square)](LICENSE)

</div>

---

## ✦ Overview

This is the full source code for the official Veldora documentation website ([https://veldora.modrao.com](https://veldora.modrao.com)).

Features:
- **Interactive Markdown Documentation** — Automatically parses `docs.md` into navigable chapters with search and keyboard shortcuts (`/`, `ArrowLeft`, `ArrowRight`).
- **Interactive Component Showcase** — Live preview of all 21 Veldora UI components with tabs for Preview, HTML Output, and Copy-Paste Snippet.
- **Modern Minimalist UI** — Pure CSS design with responsive navigation, sticky header, and dark palette.

---

## 🚀 Running Locally

```bash
# Clone the repository
git clone https://github.com/veldorahq/veldora-docs.git
cd veldora-docs

# Copy environment file
cp .env.example .env

# Install dependencies (if modifying framework bindings)
composer install

# Start development server
php -S localhost:8080 -t public/
```

Then visit `http://localhost:8080` in your browser.

---

## 📄 License & Author

- **Author**: Shahriyar Fahim
- **License**: [MIT](LICENSE)
- **Official URL**: [https://veldora.modrao.com](https://veldora.modrao.com)

# Fiov Documentation

This directory contains the source files for the Fiov documentation, built with [VitePress](https://vitepress.dev/).

## Structure

```
docs/
├── .vitepress/           # VitePress configuration
├── usage/                # Usage documentation (English)
├── de/                   # German documentation
│   ├── usage/            # Usage documentation (German)
│   └── guide/            # Guide documentation (German)
├── guide/                # Guide documentation (English)
└── index.md              # Homepage (English)
```

## Development

### Prerequisites

- Node.js 22 or later
- npm or yarn

### Installation

1. Install dependencies:
   ```bash
   npm install
   # or
   yarn
   ```

2. Start the development server:
   ```bash
   npm run docs:dev
   # or
   yarn docs:dev
   ```

3. Open `http://localhost:5173` in your browser.

### Building for Production

To generate a static version of the documentation:

```bash
npm run docs:build
# or
yarn docs:build
```

The built files will be in `docs/.vitepress/dist`.

## Adding New Content

1. **English Content**:
   - Guides: Add to the appropriate file in `docs/guide/`
   - Usage: Add to the appropriate file in `docs/usage/`

2. **German Content**:
   - Guides: Add to the appropriate file in `docs/de/guide/`
   - Usage: Add to the appropriate file in `docs/de/usage/`

## Adding a New Language

1. Create a new directory for the language code (e.g., `fr/` for French)
2. Copy the structure from `en/` or `de/`
3. Update the VitePress config in `.vitepress/config.ts` to include the new language

## Deployment

The documentation is automatically deployed when changes are pushed to the `main` branch.

## License

This documentation is available under the [MIT License](https://github.com/pascalkleindienst/fiov/blob/main/LICENSE).

# Firefly III FinTS Importer - Modernized Frontend

## Overview

This project is a PHP-based financial data importer for Firefly III that has been modernized with a Bootstrap 5 frontend. The application allows users to:
- Configure bank accounts and TAN devices
- Collect transaction data from banks
- Import transactions to Firefly III
- Review and manage imported transactions

## Project Structure

```
firefly-iii-fints-importer/
├── app/
│   └── public/
│       └── html/
│           ├── base.twig              # Main base template (Bootstrap 5)
│           ├── setup.twig             # Configuration form
│           ├── collecting-data.twig    # Data collection view
│           ├── choose-account.twig     # Account selection
│           ├── choose-2fa-device.twig  # TAN device selection
│           ├── import-progress-batched.twig  # Progress tracking
│           ├── show-transactions.twig  # Transaction review
│           └── done.twig              # Success page
├── package.json                       # Modern dev workflow
└── README.md                          # This file
```

## Modernization Summary

### Key Upgrades

1. **Framework**: Bootstrap 4 → Bootstrap 5
2. **CSS**: Added custom gradient themes and modern styling
3. **JavaScript**: Added real-time progress tracking, WebSocket support, and form state management
4. **Template Engine**: Cleaned up Twig templates with consistent structure
5. **Development Tooling**: Added Vite-based build system with ESLint/Prettier

## Development Workflow

### Prerequisites

- Node.js (v18+) - for frontend development
- npm or yarn - for package management
- Python (v3.10+) - for backend (already present)

### Installation

```bash
cd /workspace/firefly-iii-fints-importer
npm install
```

### Running the Application

```bash
# Development mode (hot reload enabled)
npm run dev

# Production build
npm run build

# Serve the built app
npm run preview
```

### Project Components

#### Base Template (`base.twig`)
- Responsive layout with card-based design
- Gradient header styles
- Semantic HTML5 structure
- Accessible form controls

#### Core Views

1. **Setup** (`setup.twig`) - Bank account configuration
2. **Data Collection** (`collecting-data.twig`) - Bank account selection and data gathering
3. **Account Selection** (`choose-account.twig`) - Account dropdown with bank info
4. **TAN Device** (`choose-2fa-device.twig`) - Two-factor authentication device selection
5. **Progress Tracking** (`import-progress-batched.twig`) - Real-time import status with WebSocket/polling
6. **Transaction Review** (`show-transactions.twig`) - Detailed transaction listing with expandable cards
7. **Completion** (`done.twig`) - Success page with persistence string

#### Features

- **Real-time Progress Bar** - Shows import percentage with animated progress
- **Transaction Review** - View and manage imported transactions
- **Persistence Management** - Save/retrieve encryption keys for future imports
- **Error Handling** - Expandable error details with full transaction logs
- **Dark Mode Ready** - Gradient colors adapt to light/dark contexts

## Usage Flow

1. **Start** → `setup.twig` - Configure bank account details
2. **Select** → `choose-account.twig` - Pick your bank account
3. **Authenticate** → `choose-2fa-device.twig` - Select TAN device
4. **Import** → `collecting-data.twig` - Gather transactions from bank
5. **Review** → `show-transactions.twig` - Check imported transactions
6. **Complete** → `done.twig` - Finalize and save persistence

## Technical Details

### Frontend Technologies
- **Bootstrap 5** - Responsive grid and component library
- **Tailwind-inspired CSS** - Custom gradient themes
- **Vanilla JavaScript** - Lightweight, no heavy frameworks
- **WebSocket Support** - Real-time progress updates (fallback to polling)

### Security Considerations
- Encryption keys stored securely (persistence strings)
- Input sanitization in Twig templates
- Proper CSP headers (via Bootstrap CDN)

## Troubleshooting

### Common Issues

**Progress bar not updating**
- Ensure WebSocket endpoint is configured at `websocketUrl` in the template
- Falls back to 3-second polling if WebSocket fails

**Transaction review not loading**
- Check that `transactions` variable is populated from the backend API
- Ensure `skip_transaction_review` is handled correctly

**Form submission errors**
- Verify `next_step` parameter is set correctly
- Check for missing required fields in the form

## Future Enhancements

- Unit tests with Jest/Python
- CI/CD pipeline integration
- Database migration support
- Multi-tenant configuration
- Export functionality (CSV, PDF reports)
- Advanced filtering and sorting of transactions

## License

MIT

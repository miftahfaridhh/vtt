# VTT — Voice-to-Text Transcription App

A web-based speech-to-text transcription application that leverages **Google Cloud Speech-to-Text API** to convert audio files into text. Built with Laravel and optimized for **Bahasa Indonesia** audio transcription.

![Laravel](https://img.shields.io/badge/Laravel-5.6-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.1%2B-777BB4?logo=php&logoColor=white)
![Google Cloud](https://img.shields.io/badge/Google%20Cloud-Speech%20API-4285F4?logo=google-cloud&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?logo=vue.js&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

---

## Features

- Upload audio files (FLAC format) via a clean web interface
- Transcription using Google Cloud Speech-to-Text API with async long-running operations
- Optimized for **Bahasa Indonesia** (`id-ID`) with automatic punctuation enabled
- Audio file storage via **Google Cloud Storage**
- Bootstrap-based responsive dashboard UI
- FFmpeg integration for audio processing

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 5.6, PHP 7.1+ |
| Frontend | Vue.js, Bootstrap 4, Axios, jQuery |
| Cloud | Google Cloud Speech-to-Text API, Google Cloud Storage |
| Audio | PHP-FFmpeg, CloudConvert |
| Build | Laravel Mix (Webpack) |

## Requirements

- PHP >= 7.1.3
- Composer
- Node.js & npm/yarn
- Google Cloud Platform account with:
  - Cloud Speech-to-Text API enabled
  - Cloud Storage bucket
  - Service account with Project > Owner role

## Installation

### 1. Clone & Install Dependencies

```bash
git clone https://github.com/miftahfaridhh/vtt.git
cd vtt
composer install
npm install
```

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your Google Cloud credentials:

```env
DB_DATABASE=vtt
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_APPLICATION_CREDENTIALS=../private/gcp_service_account.json
GOOGLE_APPLICATION_PROJECT_ID=your-gcp-project-id
GOOGLE_CLOUD_STORAGE_BUCKET=your-bucket-name
```

### 3. Google Cloud Setup

1. Create a [Google Cloud project](https://console.cloud.google.com/projectcreate)
2. Enable the **Cloud Speech-to-Text API** in [APIs & Services](https://console.cloud.google.com/apis/library)
3. Create a [Cloud Storage bucket](https://console.cloud.google.com/storage/create-bucket)
4. Create a [Service Account](https://console.cloud.google.com/iam-admin/serviceaccounts) with **Project > Owner** role and download the JSON key
5. Place the JSON key at `private/gcp_service_account.json`

### 4. Run Migrations & Build Assets

```bash
php artisan migrate
npm run dev
```

### 5. Serve

```bash
php artisan serve
```

Open `http://localhost:8000` in your browser.

## Usage

1. Navigate to the home page
2. Upload a FLAC audio file (max 100MB)
3. Submit the form and wait for transcription
4. The transcribed text will be displayed on the results page

> **Note:** The application is configured for Bahasa Indonesia by default. To support other languages, update the `languageCode` field in `app/Http/Controllers/GoogleSpeechToTextController.php`.

## Deployment

This project includes a `Procfile` for **Heroku** deployment. Ensure all environment variables are configured in your Heroku config vars before deploying.

## License

This project is open-sourced under the [MIT License](LICENSE).

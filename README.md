# MyPoolDesigner Gallery WordPress Plugin

A powerful WordPress plugin that integrates with MyPoolDesigner.ai to display beautiful, responsive pool design galleries on your website.

## Features

- **Responsive Gallery Grid**: Beautiful Bootstrap-based gallery with 4 columns on desktop, responsive down to mobile
- **Lightbox Functionality**: Click any design to view in full-screen lightbox modal
- **Multi-Image Support**: Navigate through multiple images for each design with arrow controls
- **Video Presentations**: Display and play video presentations directly in the lightbox
- **Collection Support**: Display specific design collections with custom numbering
- **Pagination**: Customizable items per page (1-100 designs)
- **Theme Support**: Light and dark theme options
- **Secure API Integration**: Connect securely with your MyPoolDesigner.ai account

## Installation

### Method 1: WordPress Admin (Recommended)

1. Download the plugin ZIP file from [MyPoolDesigner WordPress Integration](https://mypooldesigner.ai/wordpress)
2. In your WordPress admin, go to **Plugins → Add New**
3. Click **Upload Plugin** and select the downloaded ZIP file
4. Click **Install Now** and then **Activate**

### Method 2: Manual Installation

1. Download and extract the plugin files
2. Upload the `mypooldesigner-gallery` folder to your `/wp-content/plugins/` directory
3. Go to **Plugins** in your WordPress admin and activate **MyPoolDesigner Gallery**

## Setup

### 1. Get Your API Key

1. Create a free account at [MyPoolDesigner.ai](https://mypooldesigner.ai)
2. Go to **WordPress Integration** in your account dashboard
3. Generate a new API key for your website

### 2. Configure the Plugin

1. In WordPress admin, go to **Settings → MyPoolDesigner**
2. Enter your API key in the provided field
3. Click **Save Changes**
4. Verify the connection status shows "✓ Connected"

## Usage

### Basic Gallery

Display all your public designs in a responsive gallery:

```
[mypooldesigner-gallery]
```

### Gallery with Custom Pagination

Show 20 designs per page instead of the default 12:

```
[mypooldesigner-gallery pagination="20"]
```

### Dark Theme Gallery

Display the gallery with a dark theme:

```
[mypooldesigner-gallery theme="dark"]
```

### Display Specific Collection

Show designs from your first collection:

```
[mypooldesigner-collection 1]
```

### Collection with Custom Settings

Display collection with custom pagination and theme:

```
[mypooldesigner-collection 1 pagination="15" theme="light"]
```

## Shortcode Reference

### `[mypooldesigner-gallery]`

Display a paginated gallery of all your public designs.

**Parameters:**
- `pagination` (1-100): Number of designs per page (default: 12)
- `theme` (light/dark): Visual theme (default: light)

**Examples:**
```
[mypooldesigner-gallery]
[mypooldesigner-gallery pagination="24"]
[mypooldesigner-gallery theme="dark"]
[mypooldesigner-gallery pagination="16" theme="dark"]
```

### `[mypooldesigner-collection]`

Display designs from a specific collection.

**Parameters:**
- Collection number (required): Which collection to display (1 = oldest collection)
- `pagination` (1-100): Number of designs per page (default: 12)
- `theme` (light/dark): Visual theme (default: light)

**Examples:**
```
[mypooldesigner-collection 1]
[mypooldesigner-collection 2 pagination="20"]
[mypooldesigner-collection 1 theme="dark"]
[mypooldesigner-collection 3 pagination="8" theme="light"]
```

## Design Types

The plugin automatically handles different types of content:

- **Single Images**: Click to view in lightbox
- **Multi-Image Designs**: Shows image count badge, navigate with arrow keys or buttons
- **Video Presentations**: Shows video icon, plays in lightbox modal

## Responsive Design

The gallery is fully responsive:
- **Desktop**: 4 columns
- **Tablet**: 3 columns  
- **Mobile**: 2 columns
- **Small Mobile**: 1 column

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MyPoolDesigner.ai account with API access
- Active internet connection for API calls

## Troubleshooting

### "API not connected" Error

1. Verify your API key is correct
2. Check that your MyPoolDesigner.ai account has API access
3. Ensure your website can make outbound HTTP requests
4. Try regenerating your API key

### Gallery Not Displaying

1. Check that you have public designs in your MyPoolDesigner account
2. Verify the shortcode syntax is correct
3. Check browser console for JavaScript errors
4. Ensure Bootstrap CSS/JS is loading properly

### Collection Not Found

1. Verify the collection number exists (1 = first/oldest collection)
2. Check that the collection contains public designs
3. Ensure your API key has access to collections

## Support

For technical support:

1. Check the [MyPoolDesigner.ai Help Center](https://mypooldesigner.ai/support)
2. Contact support through your MyPoolDesigner.ai account
3. Submit issues on our [GitHub repository](https://github.com/mypooldesigner/wordpress)

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and updates.

## License

This plugin is licensed under the GPL v2 or later.

## Privacy

This plugin connects to MyPoolDesigner.ai servers to fetch your design data. No visitor data is collected or transmitted. Only your API key and design data are used for displaying galleries.

## Credits

Developed by the [MyPoolDesigner.ai](https://mypooldesigner.ai) team.

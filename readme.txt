=== Page Flipper ===
Contributors: willderazevedo
Tags: flipbook, digital book, interactive book, ebook, elementor
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 1.0.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Donate link: https://github.com/sponsors/willderazevedo

Create interactive digital books with flip effects and hotspots for audio, video, text, and links.

== Description ==
The **Page Flipper** is a **free** WordPress plugin that enables the creation of interactive digital books. It adds a new post type for digital books, offering a set of features to manage books and add interactivity with hotspots.

== Features ==

– **Custom Post Type:**
  – A new post type called digital books.
  – Exclusive categories for digital books.
– **Book Builder:**
  – Upload images to create pages.
  – Reorder pages.
  – Add and remove pages.
  – Add **interactive hotspots**, such as:
    – Narration
    – Audio
    – Video
    – Image
    – Text
    – Link
– **PDF Upload:**
  – Optional, to allow book download in PDF format.
– **Shortcode for Embedding:**
  – Embed the digital book anywhere on the site.
  – Default format: `[page_flipper]`
  – Optional parameters:
    
    | Parameter                   | Description                              | Possible Values | Default              |
    | --------------------------- | ---------------------------------------- | --------------- | -------------------- |
    | `id`                        | The ID of the post                       | Post ID         | Current Query        |
    | `enable_summary`            | Show or hide the summary                 | `yes` or `no`   | `yes`                |
    | `enable_related`            | Show or hide the related posts           | `yes` or `no`   | `yes`                |
    | `enable_controls`           | Show or hide the controls                | `yes` or `no`   | `yes`                |
    | `enable_share`              | Show or hide the share buttons           | `yes` or `no`   | `yes`                |
    | `enable_zoom`               | Show or hide the zoom button             | `yes` or `no`   | `yes`                |
    | `enable_background_image`   | Use or not the cover image as background | `yes` or `no`   | `yes`                |
    | `page_background_color`     | Page background color                    | Hexadecimal     | `#333333`            |
    | `page_surface_color`        | Page surface color                       | RGBA or Hex     | `rgba(0, 0, 0, 0.4)` |
    | `page_surface_accent_color` | Page surface accent color                | Hexadecimal     | `#ffffff`            |
    | `page_accent_color`         | Page accent color                        | Hexadecimal     | `#eac101`            |
    | `page_font_color`           | Page font color                          | Hexadecimal     | `#ffffff`            |

– **Elementor Integration:**
  – Widget to add digital books.
  – Support for selecting a specific book or using the current query.

== Frequently Asked Questions ==

= How do I embed a digital book? =
Use the shortcode `[page_flipper id="post_id"]` anywhere on your site.

= Can I add interactive elements to the book? =
Yes! You can add hotspots with text, images, videos, and links.

== Screenshots ==

1. **Book Builder** – A tool to create and manage digital books with an intuitive drag-and-drop interface.  
2. **Interactive Hotspots** – Add interactive elements such as text, images, videos, and links directly to book pages.  
3. **Hotspot Customization** – Configure hotspots with different content types and adjust their appearance.  
4. **Flipping Book Widget** – Embed digital books into any page or post using a customizable widget.  
5. **Hotspots in Action** – Display interactive elements that users can click to reveal additional content.  
6. **Digital Book Viewer** – Experience a fully interactive digital book with flipping pages and media elements.  

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release of the Page Flipper plugin.
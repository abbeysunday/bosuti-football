/**
 * Rich-text editor for news articles (loaded only on the admin news form).
 * Output is sanitised on the server (App\Support\RichText) before it is stored or shown.
 */
import 'trix';
import 'trix/dist/trix.css';

// Articles take a featured image instead of inline uploads, so block file attachments.
document.addEventListener('trix-file-accept', (event) => event.preventDefault());

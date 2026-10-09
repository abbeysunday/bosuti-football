<?php

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\GalleryItem;
use App\Models\NewsPost;
use App\Models\User;
use App\Support\RichText;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->forceFill(['role' => User::ROLE_ADMIN])->save();
});

$validMessage = fn (array $overrides = []) => $overrides + [
    'name' => 'Ada Obi',
    'email' => 'ada@example.com',
    'phone' => '+234 801 234 5678',
    'subject' => 'Joining a team',
    'message' => 'How can I join one of the internal teams this season?',
];

/* Contact form ---------------------------------------------------------- */

test('contact messages are saved and the club is emailed when a recipient is configured', function () use ($validMessage) {
    Mail::fake();
    config(['services.contact.notify' => 'football@bouesti.test']);

    $this->post(route('contact.store'), $validMessage())
        ->assertRedirect(route('contact'))
        ->assertSessionHas('contact_sent');

    $message = ContactMessage::firstOrFail();
    expect($message)->status->toBe('new')->subject->toBe('Joining a team');

    Mail::assertSent(ContactMessageReceived::class, fn ($mail) => $mail->hasTo('football@bouesti.test') && $mail->hasReplyTo('ada@example.com'));
});

test('without a notification address messages go to the admin inbox only', function () use ($validMessage) {
    Mail::fake();
    config(['services.contact.notify' => null]);

    $this->post(route('contact.store'), $validMessage())->assertSessionHas('contact_sent');

    expect(ContactMessage::count())->toBe(1);
    Mail::assertNothingSent();
});

test('contact form validation and honeypot', function () use ($validMessage) {
    $this->post(route('contact.store'), ['name' => '', 'email' => 'nope', 'message' => 'short'])
        ->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

    $this->post(route('contact.store'), $validMessage(['website' => 'http://spam.example']))
        ->assertSessionHasErrors('website');

    expect(ContactMessage::count())->toBe(0);
});

test('the contact page shows the confirmation after sending', function () use ($validMessage) {
    $this->followingRedirects()->post(route('contact.store'), $validMessage())
        ->assertOk()
        ->assertSee('Message sent')
        ->assertSee('Thank you, Ada Obi.');
});

test('admins manage messages in the inbox; others cannot see them', function () use ($validMessage) {
    $this->post(route('contact.store'), $validMessage());
    $message = ContactMessage::firstOrFail();

    $this->actingAs(User::factory()->create())->get(route('admin.contact-messages.index'))->assertForbidden();

    $this->actingAs($this->admin);
    $this->get(route('admin.dashboard'))->assertSee('1 unread message');
    $this->get(route('admin.contact-messages.index'))->assertOk()->assertSee('Ada Obi');
    $this->get(route('admin.contact-messages.show', $message))->assertOk()->assertSee('How can I join');
    expect($message->fresh()->status)->toBe('read');

    $this->put(route('admin.contact-messages.update', $message), ['status' => 'replied', 'admin_notes' => 'Sent trial dates.'])
        ->assertSessionHasNoErrors();
    expect($message->fresh()->status)->toBe('replied');

    $this->delete(route('admin.contact-messages.destroy', $message))->assertRedirect(route('admin.contact-messages.index'));
    expect(ContactMessage::count())->toBe(0);
});

/* Image resizing -------------------------------------------------------- */

test('uploaded photos are resized and converted to webp', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)->post(route('admin.gallery.store'), [
        'images' => [UploadedFile::fake()->image('phone-photo.jpg', 4000, 3000)],
        'category' => 'match',
    ])->assertSessionHasNoErrors();

    $path = GalleryItem::firstOrFail()->image;
    expect($path)->toStartWith('gallery/')->toEndWith('.webp');

    [$width, $height] = getimagesizefromstring(Storage::disk('public')->get($path));
    expect($width)->toBe(2000)->and($height)->toBe(1500);
});

test('small images are never enlarged', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)->post(route('admin.gallery.store'), [
        'images' => [UploadedFile::fake()->image('small.png', 300, 200)],
    ])->assertSessionHasNoErrors();

    [$width, $height] = getimagesizefromstring(Storage::disk('public')->get(GalleryItem::firstOrFail()->image));
    expect([$width, $height])->toBe([300, 200]);
});

/* Rich text ------------------------------------------------------------- */

test('rich text keeps formatting but strips scripts, event handlers and javascript links', function () {
    $clean = RichText::sanitize(
        '<h1>Derby day</h1><div><strong>Bold</strong> and <em>italic</em><br><a href="https://bouesti.edu.ng">site</a></div>'
        . '<script>alert(1)</script><img src=x onerror="alert(2)"><a href="javascript:alert(3)">bad</a>'
        . '<div onclick="alert(4)" style="color:red">clicky</div><iframe src="https://evil.example"></iframe>'
    );

    expect($clean)
        ->toContain('<h1>Derby day</h1>')
        ->toContain('<strong>Bold</strong>')
        ->toContain('<em>italic</em>')
        ->toContain('href="https://bouesti.edu.ng"')
        ->toContain('rel="noopener noreferrer nofollow"')
        ->not->toContain('<script')
        ->not->toContain('onerror')
        ->not->toContain('javascript:')
        ->not->toContain('onclick')
        ->not->toContain('style=')
        ->not->toContain('<iframe')
        ->not->toContain('<img');
});

test('articles written in the editor are stored clean and rendered as html', function () {
    $this->actingAs($this->admin)->post(route('admin.news.store'), [
        'title' => 'Editor article',
        'content' => '<div>Intro <strong>paragraph</strong></div><h1>Key moments</h1><ul><li>Early goal</li></ul><script>alert(1)</script>',
        'is_published' => 1,
    ])->assertSessionHasNoErrors();

    $post = NewsPost::firstOrFail();
    expect($post->getRawOriginal('content'))->not->toContain('<script');

    $this->get(route('news.show', $post))
        ->assertOk()
        ->assertSee('<strong>paragraph</strong>', false)
        ->assertSee('<h1>Key moments</h1>', false)
        ->assertSee('<li>Early goal</li>', false)
        ->assertDontSee('alert(1)', false);
});

test('an empty editor is rejected', function () {
    $this->actingAs($this->admin)->post(route('admin.news.store'), [
        'title' => 'Empty', 'content' => '<div><br></div>',
    ])->assertSessionHasErrors('content');
});

test('older plain-text articles still render safely', function () {
    $post = NewsPost::create([
        'title' => 'Legacy post',
        'content' => "First paragraph with <b>tags</b> typed as text.\n\n## Second half\n\n> A quote",
        'is_published' => true,
    ]);

    expect(RichText::isHtml($post->getRawOriginal('content')))->toBeTrue(); // contains <b>, so it is sanitised on save

    $plain = NewsPost::create(['title' => 'Plain', 'content' => "Line one\n\n## Heading\n\n> Quote & more", 'is_published' => true]);
    $this->get(route('news.show', $plain))
        ->assertOk()
        ->assertSee('<h2>Heading</h2>', false)
        ->assertSee('<blockquote>Quote &amp; more</blockquote>', false);
});

test('the news editor screen loads the editor', function () {
    $this->actingAs($this->admin)->get(route('admin.news.create'))
        ->assertOk()
        ->assertSee('<trix-editor', false)
        ->assertSee('editor-', false);
});

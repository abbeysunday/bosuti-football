<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <img src="{{ asset('frontend/assets/images/bouesti-fc-crest.png') }}" alt="BOUESTI Sports crest" width="70" height="70" loading="lazy">
                    <div>
                        <b>Bamidele Olumilua University<br>of Education, Science and Technology</b>
                        <span>BOUESTI SPORTS</span>
                    </div>
                </div>
                <p class="footer-copy">Pride of Ikere. Passion for the Game. The home of football and basketball at BOUESTI, built around talent, character, community and student development.</p>
                <div class="socials">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                    <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube" aria-hidden="true"></i></a>
                    <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok" aria-hidden="true"></i></a>
                </div>
            </div>

            <div>
                <div class="footer-title" id="footer-quick">Quick Links</div>
                <nav class="footer-links" aria-labelledby="footer-quick">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('about') }}">Club</a>
                    <a href="{{ route('teams.index') }}">Football</a>
                    <a href="{{ route('basketball') }}">Basketball</a>
                    <a href="{{ route('league.table') }}">League Table</a>
                    <a href="{{ route('fixtures') }}">Fixtures</a>
                    <a href="{{ route('news') }}">News</a>
                </nav>
            </div>

            <div>
                <div class="footer-title" id="footer-explore">Explore</div>
                <nav class="footer-links" aria-labelledby="footer-explore">
                    <a href="{{ route('gallery') }}">Media</a>
                    <a href="{{ route('membership') }}">Fans</a>
                    <a href="{{ route('academy') }}">Development</a>
                    <a href="{{ route('trials') }}">Trials</a>
                    <a href="{{ route('contact') }}">Contact</a>
                </nav>
            </div>

            <div>
                <div class="footer-title">Get in Touch</div>
                <div class="footer-links">
                    <span>BOUESTI, Ikere-Ekiti</span>
                    <span>Ekiti State, Nigeria</span>
                    <a href="{{ route('contact') }}">Contact us</a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span>&copy; {{ date('Y') }} BOUESTI Sports. All rights reserved.</span>
            <span>A Bamidele Olumilua University of Education, Science and Technology initiative.</span>
        </div>
    </div>
</footer>

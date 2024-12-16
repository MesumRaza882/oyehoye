<footer>
    <div style="position:absolute;right:0px">
        <svg width="200" height="259" viewBox="0 0 200 259" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path opacity="0.1" d="M302.69 222.016C60.7536 333.008 47.9011 115.051 114.406 73.4315C157.345 46.5603 184.59 83.524 163.067 103.393C141.545 123.262 -35.2734 133.291 14.8309 -51.5344" stroke="white" stroke-width="10.1438" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
    <div style="position:absolute;bottom:100px">
        <svg width="300" height="234" viewBox="0 0 300 234" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g style="mix-blend-mode:overlay">
        <path d="M-102 16.2699C-26.8563 11.2929 148.269 -30.0197 144.684 115.753C142.37 209.869 38.9232 221.386 28.0001 168.5C17.0771 115.614 194.93 104.978 294.873 229.516" stroke="white" stroke-width="13"/>
        </g>
        </svg>
    </div>

    <div class="container py-70px pb-2">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-widgets">
                    <div class="brand-logo mt-1 mb-8 ">
                        <a href="#">
                            <img src="icons/logo.png" alt="RespectMart" class="light-version-logo" width="200" />
                        </a>
                    </div>
                    <p class="footer-widgets__text">
                        RespectMart is a state of the art technology that connects
                        buyers to major e-commerce platforms. We completely eliminate
                        the hassle that comes with international shopping and slow
                        shipping. We are at the middle to give you a great shopping
                        experience!
                    </p>
                    <img src="icons/stripepay-white.png" class="zi-1 w-100" />
                    <div class="row mt-3 ">
                        <div class="col-6 zi-1">
                            <img src="icons/apple.png" class="w-100" />
                        </div>
                        <div class="col-6 zi-1">
                            <img src="icons/google.png" class="w-100" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row ps-4">
                    <div class="col-md-4 col-xs-6">
                        <div class="footer-widgets">
                            <h4 class="footer-widgets__title">Company</h4>
                            <ul class="footer-widgets__list">
                                <li>
                                    <router-link to="/about" v-slot="{ href }"><a :href="href">About us</a></router-link>
                                </li>
                                <li>
                                    <a href="https://sell.respectmart.com/terms.html">Terms &amp; Conditions</a>
                                </li>
                                <li>
                                    <a href="https://sell.respectmart.com/privacy-policy.html">Privacy Policy</a>
                                </li>
                                <li>
                                    <router-link to="/account/track-shipping"v-slot="{ href }"><a :href="href">Track Shipping</a></router-link>
                                </li>
                                <li>
                                    <router-link to="/contact" v-slot="{ href }"><a :href="href">Contact Us</a></router-link>
                                </li>
                                <li>
                                    <a href="https://app.respectmart.com/cards">Cards</a>
                                </li>
                                <li>
                                    <a href="https://app.respectmart.com/shipping">Shipping</a>
                                </li>
                                <li>
                                    <a href="https://sell.respectmart.com/aml.html">AML</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-6">
                        <div class="footer-widgets footer-widgets--1">
                            <h4 class="footer-widgets__title">Contact Details</h4>
                            <ul class="footer-widgets__list footer-widgets--address">
                                <li>
                                    <i class="fa fa-map-marker-alt mt-2"></i>
                                    <div class="list-content">
                                        <b>USA:</b> 1751D W Howard St Chicago #230, IL 60626<br />
                                        <b>NIGERIA:</b> 42, Montgomery Road 2nd Flr. Yaba, Lagos
                                    </div>
                                </li>
                                <li>
                                    <i class="fa fa-phone-alt mt-3"></i>
                                    <div class="list-content">
                                        <a href="tel: +2347035952753">+234 703 595 2753</a>
                                        <span> 7 Days - <b>8AM to 10PM</b> </span>
                                    </div>
                                </li>
                                <li>
                                    <i class="fa fa-envelope mt-3"></i>
                                    <a href="mailto:info@respectmart.com"
                                        >info@respectmart.com</a
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-6">
                        <div class="footer-widgets">
                            <h4 class="footer-widgets__title">Social</h4>
                            <ul class="footer-widgets__list">
                                <li>
                                    <a href="#">Facebook</a>
                                </li>
                                <li>
                                    <a href="#">Twitter</a>
                                </li>
                                <li>
                                    <a href="#">Instagam</a>
                                </li>
                                <li>
                                    <a href="#">Linkedin</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="mt-5 mb-3" style="border: 1px solid rgba(255, 255, 255, 1) !important" />
        <div class="">
            <div class="row">
                <div class="col-12">
                    <div class="copyright text-center text-md-start">
                        <p class="text-white">
                            © 2022 Afridext Integrated Services LLC, All Rights Reserved
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
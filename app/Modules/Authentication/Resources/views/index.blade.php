

@extends('authentication::layouts.front.general')
@section('title')
LMS
@endsection
@section('content')


  <!-- Useful features: Start -->
  <section id="landingFeatures " class="section-py  mt-3 landing-features">
    <div class="container">
      <div class="text-center mb-3 pb-1">
        <span class="badge bg-label-primary">Useful Features</span>
      </div>
      <h3 class="text-center mb-1">
        <span class="section-title">Everything you need</span> to start your next project
      </h3>
      <p class="text-center mb-3 mb-md-5 pb-3">
        Not just a set of tools, the package includes ready-to-deploy conceptual application.
      </p>
      <div class="features-icon-wrapper row gx-0 gy-4 g-sm-5">
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="text-center mb-3">
            <img src="../../assets/img/front-pages/icons/laptop.png" alt="laptop charging" />
          </div>
          <h5 class="mb-3">Quality Code</h5>
          <p class="features-icon-description">
            Code structure that all developers will easily understand and fall in love with.
          </p>
        </div>
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="text-center mb-3">
            <img src="../../assets/img/front-pages/icons/rocket.png" alt="transition up" />
          </div>
          <h5 class="mb-3">Continuous Updates</h5>
          <p class="features-icon-description">
            Free updates for the next 12 months, including new demos and features.
          </p>
        </div>
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="text-center mb-3">
            <img src="../../assets/img/front-pages/icons/paper.png" alt="edit" />
          </div>
          <h5 class="mb-3">Stater-Kit</h5>
          <p class="features-icon-description">
            Start your project quickly without having to remove unnecessary features.
          </p>
        </div>
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="text-center mb-3">
            <img src="../../assets/img/front-pages/icons/check.png" alt="3d select solid" />
          </div>
          <h5 class="mb-3">API Ready</h5>
          <p class="features-icon-description">
            Just change the endpoint and see your own data loaded within seconds.
          </p>
        </div>
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="text-center mb-3">
            <img src="../../assets/img/front-pages/icons/user.png" alt="lifebelt" />
          </div>
          <h5 class="mb-3">Excellent Support</h5>
          <p class="features-icon-description">An easy-to-follow doc with lots of references and code examples.</p>
        </div>
        <div class="col-lg-4 col-sm-6 text-center features-icon-box">
          <div class="text-center mb-3">
            <img src="../../assets/img/front-pages/icons/keyboard.png" alt="google docs" />
          </div>
          <h5 class="mb-3">Well Documented</h5>
          <p class="features-icon-description">An easy-to-follow doc with lots of references and code examples.</p>
        </div>
      </div>
    </div>
  </section>
  <!-- Useful features: End -->


  <!-- Our great team: Start -->
  <section id="landingTeam" class="section-py landing-team">
    <div class="container">
      <div class="text-center mb-3 pb-1">
        <span class="badge bg-label-primary">Our Great Team</span>
      </div>
      <h3 class="text-center mb-1"><span class="section-title">Supported</span> by Real People</h3>
      <p class="text-center mb-md-5 pb-3">Who is behind these great-looking interfaces?</p>
      <div class="row gy-5 mt-2">
        <div class="col-lg-3 col-sm-6">
          <div class="card mt-3 mt-lg-0 shadow-none">
            <div class="bg-label-primary position-relative team-image-box">
              <img
                src="../../assets/img/front-pages/landing-page/team-member-1.png"
                class="position-absolute card-img-position bottom-0 start-50 scaleX-n1-rtl"
                alt="human image" />
            </div>
            <div class="card-body border border-top-0 border-label-primary text-center">
              <h5 class="card-title mb-0">Sophie Gilbert</h5>
              <p class="text-muted mb-0">Project Manager</p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="card mt-3 mt-lg-0 shadow-none">
            <div class="bg-label-info position-relative team-image-box">
              <img
                src="../../assets/img/front-pages/landing-page/team-member-2.png"
                class="position-absolute card-img-position bottom-0 start-50 scaleX-n1-rtl"
                alt="human image" />
            </div>
            <div class="card-body border border-top-0 border-label-info text-center">
              <h5 class="card-title mb-0">Paul Miles</h5>
              <p class="text-muted mb-0">UI Designer</p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="card mt-3 mt-lg-0 shadow-none">
            <div class="bg-label-danger position-relative team-image-box">
              <img
                src="../../assets/img/front-pages/landing-page/team-member-3.png"
                class="position-absolute card-img-position bottom-0 start-50 scaleX-n1-rtl"
                alt="human image" />
            </div>
            <div class="card-body border border-top-0 border-label-danger text-center">
              <h5 class="card-title mb-0">Nannie Ford</h5>
              <p class="text-muted mb-0">Development Lead</p>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="card mt-3 mt-lg-0 shadow-none">
            <div class="bg-label-success position-relative team-image-box">
              <img
                src="../../assets/img/front-pages/landing-page/team-member-4.png"
                class="position-absolute card-img-position bottom-0 start-50 scaleX-n1-rtl"
                alt="human image" />
            </div>
            <div class="card-body border border-top-0 border-label-success text-center">
              <h5 class="card-title mb-0">Chris Watkins</h5>
              <p class="text-muted mb-0">Marketing Manager</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Our great team: End -->



  <!-- Fun facts: Start -->
  <section id="landingFunFacts" class="section-py landing-fun-facts">
    <div class="container">
      <div class="row gy-3">
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-label-primary shadow-none">
            <div class="card-body text-center">
              <img src="../../assets/img/front-pages/icons/laptop.png" alt="laptop" class="mb-2" />
              <h5 class="h2 mb-1">7.1k+</h5>
              <p class="fw-medium mb-0">
                Support Tickets<br />
                Resolved
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-label-success shadow-none">
            <div class="card-body text-center">
              <img src="../../assets/img/front-pages/icons/user-success.png" alt="laptop" class="mb-2" />
              <h5 class="h2 mb-1">50k+</h5>
              <p class="fw-medium mb-0">
                Join creatives<br />
                community
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-label-info shadow-none">
            <div class="card-body text-center">
              <img src="../../assets/img/front-pages/icons/diamond-info.png" alt="laptop" class="mb-2" />
              <h5 class="h2 mb-1">4.8/5</h5>
              <p class="fw-medium mb-0">
                Highly Rated<br />
                Products
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="card border border-label-warning shadow-none">
            <div class="card-body text-center">
              <img src="../../assets/img/front-pages/icons/check-warning.png" alt="laptop" class="mb-2" />
              <h5 class="h2 mb-1">100%</h5>
              <p class="fw-medium mb-0">
                Money Back<br />
                Guarantee
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Fun facts: End -->

  <!-- FAQ: Start -->
  <section id="landingFAQ" class="section-py bg-body landing-faq">
    <div class="container">
      <div class="text-center mb-3 pb-1">
        <span class="badge bg-label-primary">FAQ</span>
      </div>
      <h3 class="text-center mb-1">Frequently asked <span class="section-title">questions</span></h3>
      <p class="text-center mb-5 pb-3">Browse through these FAQs to find answers to commonly asked questions.</p>
      <div class="row gy-5">
        <div class="col-lg-5">
          <div class="text-center">
            <img
              src="../../assets/img/front-pages/landing-page/faq-boy-with-logos.png"
              alt="faq boy with logos"
              class="faq-image" />
          </div>
        </div>
        <div class="col-lg-7">
          <div class="accordion" id="accordionExample">
            <div class="card accordion-item active">
              <h2 class="accordion-header" id="headingOne">
                <button
                  type="button"
                  class="accordion-button"
                  data-bs-toggle="collapse"
                  data-bs-target="#accordionOne"
                  aria-expanded="true"
                  aria-controls="accordionOne">
                  Do you charge for each upgrade?
                </button>
              </h2>

              <div id="accordionOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  Lemon drops chocolate cake gummies carrot cake chupa chups muffin topping. Sesame snaps icing
                  marzipan gummi bears macaroon dragée danish caramels powder. Bear claw dragée pastry topping
                  soufflé. Wafer gummi bears marshmallow pastry pie.
                </div>
              </div>
            </div>
            <div class="card accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button
                  type="button"
                  class="accordion-button collapsed"
                  data-bs-toggle="collapse"
                  data-bs-target="#accordionTwo"
                  aria-expanded="false"
                  aria-controls="accordionTwo">
                  Do I need to purchase a license for each website?
                </button>
              </h2>
              <div
                id="accordionTwo"
                class="accordion-collapse collapse"
                aria-labelledby="headingTwo"
                data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  Dessert ice cream donut oat cake jelly-o pie sugar plum cheesecake. Bear claw dragée oat cake
                  dragée ice cream halvah tootsie roll. Danish cake oat cake pie macaroon tart donut gummies. Jelly
                  beans candy canes carrot cake. Fruitcake chocolate chupa chups.
                </div>
              </div>
            </div>
            <div class="card accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button
                  type="button"
                  class="accordion-button collapsed"
                  data-bs-toggle="collapse"
                  data-bs-target="#accordionThree"
                  aria-expanded="false"
                  aria-controls="accordionThree">
                  What is regular license?
                </button>
              </h2>
              <div
                id="accordionThree"
                class="accordion-collapse collapse"
                aria-labelledby="headingThree"
                data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  Regular license can be used for end products that do not charge users for access or service(access
                  is free and there will be no monthly subscription fee). Single regular license can be used for
                  single end product and end product can be used by you or your client. If you want to sell end
                  product to multiple clients then you will need to purchase separate license for each client. The
                  same rule applies if you want to use the same end product on multiple domains(unique setup). For
                  more info on regular license you can check official description.
                </div>
              </div>
            </div>
            <div class="card accordion-item">
              <h2 class="accordion-header" id="headingFour">
                <button
                  type="button"
                  class="accordion-button collapsed"
                  data-bs-toggle="collapse"
                  data-bs-target="#accordionFour"
                  aria-expanded="false"
                  aria-controls="accordionFour">
                  What is extended license?
                </button>
              </h2>
              <div
                id="accordionFour"
                class="accordion-collapse collapse"
                aria-labelledby="headingFour"
                data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis et aliquid quaerat possimus maxime!
                  Mollitia reprehenderit neque repellat deleniti delectus architecto dolorum maxime, blanditiis
                  earum ea, incidunt quam possimus cumque.
                </div>
              </div>
            </div>
            <div class="card accordion-item">
              <h2 class="accordion-header" id="headingFive">
                <button
                  type="button"
                  class="accordion-button collapsed"
                  data-bs-toggle="collapse"
                  data-bs-target="#accordionFive"
                  aria-expanded="false"
                  aria-controls="accordionFive">
                  Which license is applicable for SASS application?
                </button>
              </h2>
              <div
                id="accordionFive"
                class="accordion-collapse collapse"
                aria-labelledby="headingFive"
                data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  Lorem ipsum dolor sit amet consectetur, adipisicing elit. Sequi molestias exercitationem ab cum
                  nemo facere voluptates veritatis quia, eveniet veniam at et repudiandae mollitia ipsam quasi
                  labore enim architecto non!
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- FAQ: End -->



  <!-- Contact Us: Start -->
  <section id="landingContact" class="section-py bg-body landing-contact">
    <div class="container">
      <div class="text-center mb-3 pb-1">
        <span class="badge bg-label-primary">Contact US</span>
      </div>
      <h3 class="text-center mb-1"><span class="section-title">Let's work</span> together</h3>
      <p class="text-center mb-4 mb-lg-5 pb-md-3">Any question or remark? just write us a message</p>
      <div class="row gy-4">
        <div class="col-lg-5">
          <div class="contact-img-box position-relative border p-2 h-100">
            <img
              src="../../assets/img/front-pages/landing-page/contact-customer-service.png"
              alt="contact customer service"
              class="contact-img w-100 scaleX-n1-rtl" />
            <div class="pt-3 px-4 pb-1">
              <div class="row gy-3 gx-md-4">
                <div class="col-md-6 col-lg-12 col-xl-6">
                  <div class="d-flex align-items-center">
                    <div class="badge bg-label-primary rounded p-2 me-2"><i class="ti ti-mail ti-sm"></i></div>
                    <div>
                      <p class="mb-0">Email</p>
                      <h5 class="mb-0">
                        <a href="mailto:example@gmail.com" class="text-heading">example@gmail.com</a>
                      </h5>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-12 col-xl-6">
                  <div class="d-flex align-items-center">
                    <div class="badge bg-label-success rounded p-2 me-2">
                      <i class="ti ti-phone-call ti-sm"></i>
                    </div>
                    <div>
                      <p class="mb-0">Phone</p>
                      <h5 class="mb-0"><a href="tel:+1234-568-963" class="text-heading">+1234 568 963</a></h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-7">
          <div class="card">
            <div class="card-body">
              <h4 class="mb-1">Send a message</h4>
              <p class="mb-4">
                If you would like to discuss anything related to payment, account, licensing,<br
                  class="d-none d-lg-block" />
                partnerships, or have pre-sales questions, you’re at the right place.
              </p>
              <form>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label" for="contact-form-fullname">Full Name</label>
                    <input type="text" class="form-control" id="contact-form-fullname" placeholder="john" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="contact-form-email">Email</label>
                    <input
                      type="text"
                      id="contact-form-email"
                      class="form-control"
                      placeholder="johndoe@gmail.com" />
                  </div>
                  <div class="col-12">
                    <label class="form-label" for="contact-form-message">Message</label>
                    <textarea
                      id="contact-form-message"
                      class="form-control"
                      rows="8"
                      placeholder="Write a message"></textarea>
                  </div>
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary">Send inquiry</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection



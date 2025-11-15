<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="./assets/camera.png" type="image/x-icon">
  <link rel="stylesheet" href="../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
  <title>Aperture</title>
</head>

<body class="">
  <!------------------------------------------- NAV ------------------------------------------------>
  <?php include './includes/header.php'; ?>

  <!------------------------------------------- NAV ------------------------------------------------>


  <!--------------------------------------- HOME SECTION-------------------------------------------->


  <section id="home" class="home w-100 min-vh-100 bg-dark position-relative d-flex justify-content-center align-items-center">

    <div class="overlay"></div>
    <div class="container position-absolute text-center text-light d-flex align-items-center justify-content-center flex-column gap-3" id="homeText">
      <div class="m-0">
            <small class="d-inline-block py-2 px-3 rounded-pill bg-opacity-10 bg-light "  style="border: 1px solid var(--light);">Aperture Studios</small>
        </div>
      <h1 class="display-1 w-75 fw-semibold serif m-0 text-light">Capture Every Moment, Book Every Memory</h1>
      <div class="d-flex flex-column flex-md-row gap-3">
        <a href="login.php" class="btn border-light px-4 text-light rounded">Book Now</a>
        <a href="aboutCompany.php" class="btn bg-light text-dark px-4 rounded">Learn More</a>
      </div>
    </div>

  </section>

  <!--------------------------------------- HOME SECTION-------------------------------------------->

  <!--------------------------------------- Problem Statement SECTION-------------------------------------------->


  <section class=" py-5 min-vh-100 w-100 d-flex flex-column gap-5 position-relative justify-content-center align-items-center" id="problem">

    <div class="container p-md-4 p-2 d  m-0 bg-white rounded-5">
      <div class="row g-lg-5 g-3 p-2 p-md-5 d-flex justify-content-center align-items-center">

        <div class=" col-lg-6 m-0 py-2">

          <h2 class="fw-bolder serif">Finding the Perfect Photographer Shouldn't Be This Hard</h2>

          <p>You have a vision for your special day, but finding the right creative professional who understands your style, is available on your date, and fits your budget feels impossible.</p>


          <ul class="list-unstyled mt-4">
            <li class="mb-2">
              <img src="./assets/x.png" class="me-2" alt="" style="width: 1rem; height: 1rem;">Endless searching through countless portfolios
            </li>

            <li class="mb-2">
              <img src="./assets/x.png" class="me-2" alt="" style="width: 1rem; height: 1rem;">Uncertainty about pricing and hidden fees
            </li>

            <li class="mb-2">
              <img src="./assets/x.png" class="me-2" alt="" style="width: 1rem; height: 1rem;">No real-time availability information
            </li>

            <li class="mb-2">
              <img src="./assets/x.png" class="me-2" alt="" style="width: 1rem; height: 1rem;">Difficulty comparing styles and specialties
            </li>

            <li class="">
              <img src="./assets/x.png" class="me-2" alt="" style="width: 1rem; height: 1rem;">Stress about delivery timelines and quality
            </li>
          </ul>

        </div>

        <div class=" col-lg-6 m-0">
          <img src="./assets/high-angle-photo-camera-indoors-still-life.jpg" alt="" class="img-fluid rounded-5">
        </div>

      </div>
    </div>


    <div class="container m-0 bg-white p-md-5 p-4 rounded-5">
      <div class="row mb-5">
        <div class="text-center mx-auto col-lg-8">
          <h2 class="fw-bolder serif">Easy Booking for Photographers and Videographers</h2>
          <p class="text-secondary">We help you find, compare, and book trusted professionals with clear prices and real-time availability. Quality work and on-time delivery are guaranteed.</p>
        </div>

      </div>

      <div class="row solutionGroup gap-3 p-md-3">
        <div class="card solutionCard  px-2 py-4 shadow border-0">
          <img src="./assets/pro.png" alt="" class="card-img-top mx-auto" style="width: 4rem;">
          <div class="card-body text-center">
            <h4 class="card-title">Trusted Professionals</h4>
            <small class="card-text text-secondary">We connect you with experienced photographers and videographers you can rely on.</small>
          </div>
        </div>

        <div class="card solutionCard px-2 py-4 shadow border-0">
          <img src="./assets/price.png" alt="" class="card-img-top mx-auto" style="width: 4rem;">
          <div class="card-body text-center">
            <h4 class="card-title">Clear Pricing</h4>
            <small class="card-text text-secondary">See all prices upfront with no hidden fees, so you can plan your budget easily.</small>
          </div>
        </div>

        <div class="card solutionCard  px-2 py-4 shadow border-0">
          <img src="./assets/booking.png" alt="" class="card-img-top mx-auto" style="width: 4rem;">
          <div class="card-body text-center">
            <h4 class="card-title">Real-Time Booking</h4>
            <small class="card-text text-secondary">Check availability and book your preferred professional instantly online.</small>
          </div>
        </div>


        <div class="card solutionCard  px-2 py-4 shadow border-0">
          <img src="./assets/compare.png" alt="" class="card-img-top mx-auto" style="width: 4rem;">
          <div class="card-body text-center">
            <h4 class="card-title">Easy Style Comparison</h4>
            <small class="card-text text-secondary">Filter and compare creatives by style, specialty, and reviews to find your perfect match.</small>
          </div>
        </div>

        <div class="card solutionCard  px-2 py-4 shadow border-0">
          <img src="./assets/quality.png" alt="" class="card-img-top mx-auto" style="width: 4rem;">
          <div class="card-body text-center">
            <h4 class="card-title">Quality & On-Time Delivery</h4>
            <small class="card-text text-secondary">We guarantee high-quality photos and videos delivered on schedule.</small>
          </div>
        </div>

      </div>



    </div>
  </section>

  <!------------------------------------- SERVICES SECTION-------------------------------------------->


  <section class="w-100 py-5  min-vh-100 d-flex flex-column justify-content-center align-items-center " id="services">


    <div class="container py-5 px-4  m-0 bg-light justify-content-center align-items-center rounded-5 shadow">


      <div class="row gap-2 mb-2 justify-content-center">
        <div class="col-lg-5 rounded align-content-end p-3">
          <h1 class="fw-bold serif">Personalized Sessions for Life's Milestones</h1>
          <p>Whether it's a romantic engagement or a family celebration, our services ensure high-quality captures with expert editing and on-time delivery. Start with our 4-step booking process for hassle-free planning.</p>
        </div>

        <div class="col-lg-3 col-md-5 bg-secondary rounded-4 position-relative">
          <img src="./assets/pexels-emma-bauso-1183828-2253831.jpg" alt="">
          <h6 class="bg-light p-1 rounded">Weddings & Engagements</h6>
        </div>

        <div class="col-lg-3 col-md-5 bg-secondary rounded-4 position-relative">
          <img src="./assets/pexels-rdne-7648020.jpg" alt="">
          <h6 class="bg-light p-1 rounded">Creative Shoots</h6>
        </div>

      </div>

      <div class="row gap-2 mb-2 justify-content-center">

        <div class="col-md-3 bg-secondary rounded-4 position-relative">
          <img src="./assets/pexels-jessbaileydesign-768472.jpg" alt="">
          <h6 class="bg-light p-1 rounded">Birthdays & Celebrations</h6>
        </div>

        <div class="col-md-5 bg-secondary rounded-4 position-relative">
          <img src="./assets/pexels-cottonbro-5077049.jpg" alt="">
          <h6 class="bg-light p-1 rounded">Corporate Events</h6>
        </div>

        <div class="col-md-3 bg-secondary rounded-4 position-relative">
          <img src="./assets/high-angle-photo-camera-indoors-still-life.jpg" alt="">
          <h6 class="bg-light p-1 rounded">Behind the Lens</h6>
        </div>
      </div>

    </div>

  </section>


  <!------------------------------------- SERVICES SECTION-------------------------------------------->

  <!------------------------------------- FEATURES SECTION-------------------------------------------->

  <section id="features" class="px-2 py-5 position-relative bg-white  w-100 min-vh-100 justify-content-center align-content-center">
    <div class="container">

      <div class="row justify-content-center mb-5">
        <div class="col-md-8 text-center">
          <h1 class="display-4 serif">Powerful Features Designed for Your Needs</h1>
          <p>Our platform includes everything you need to find, book, and work with the perfect photographer or videographer for your event.</p>
        </div>
      </div>

      <div class="row justify-content-center gap-3">
        <div class="card col-md-5 px-3 py-4 shadow border-0 justify-content-start">
          <img src="./assets/cam.png" alt="" class="card-img-top mb-3" style="width: 4rem;">
          <div class="card-body text-start p-0">
            <h4 class="card-title">Professional Photography</h4>
            <p class="card-text">High-quality photos with expert composition, lighting, and editing to preserve your memories.</p>
          </div>
        </div>

        <div class="card col-md-5 px-3 py-4 shadow border-0">
          <img src="./assets/vid.png" alt="" class="card-img-top mb-3" style="width: 4rem;">
          <div class="card-body text-start p-0">
            <h4 class="card-title">Creative Videography</h4>
            <p class="card-text">Engaging videos that tell your story with cinematic techniques and smooth editing.</p>
          </div>
        </div>

        <div class="card col-md-5 px-3 py-4 shadow border-0">
          <img src="./assets/edit.png" alt="" class="card-img-top mb-3" style="width: 4rem;">
          <div class="card-body text-start p-0">
            <h4 class="card-title">Custom Editing</h4>
            <p class="card-text">Tailored photo retouching and video editing to match your style and vision.</p>
          </div>
        </div>

        <div class="card col-md-5 px-3 py-4 shadow border-0">
          <img src="./assets/event.png" alt="" class="card-img-top mb-3" style="width: 4rem;">
          <div class="card-body text-start p-0">
            <h4 class="card-title">Event Coverage</h4>
            <p class="card-text">Comprehensive coverage for weddings, corporate events, parties, and more.</p>
          </div>
        </div>

      </div>

    </div>
  </section>

  <!------------------------------------- FEATURES SECTION-------------------------------------------->

  <!------------------------------------- How it works SECTION-------------------------------------------->

  <section class="py-5 bg-dark howItWorks" id="howItWorks">
    <div class="container">
      <div class="row justify-content-center mb-5">
        <div class="col-md-8 text-center text-light">
          <h1 class="display-4 serif">How it works</h1>
          <p class="text-light">Booking your perfect photographer or videographer has never been easier. Follow these simple steps to capture every moment and create lasting memories.</p>
        </div>
      </div>
      <div class="row justify-content-center align-items-center gap-2">

        <div class="col-md-3 text-center shadow p-2 rounded">
          <img src="./assets/1.png" alt="" style="width: 3rem;">
          <h4 class="my-3">Submit Your Request</h4>
          <p>Fill out a simple form with your event details and preferred date.</p>
        </div>
        <div class="col-md-3 text-center shadow p-2 rounded">
          <img src="./assets/2.png" alt="" style="width: 3rem;">
          <h4 class="my-3">We Review & Confirm</h4>
          <p>Our team reviews your request and confirms availability before approval.</p>
        </div>
        <div class="col-md-3 text-center shadow p-2 rounded">
          <img src="./assets/3.png" alt="" style="width: 3rem;">
          <h4 class="my-3">Booking Approved</h4>
          <p>Receive confirmation once your appointment is secured.</p>
        </div>

        <div class="col-md-3 text-center shadow p-2 rounded">
          <img src="./assets/4.png" alt="" style="width: 3rem;">
          <h4 class="my-3">Enjoy Your Event</h4>
          <p>Relax and let our professionals capture your special moments perfectly.</p>
        </div>
      </div>

    </div>
  </section>

  <!------------------------------------- How it works SECTION-------------------------------------------->

  <!---------------------------------- ABOUT COMPANY SECTION------------------------------------------>


  <!-- <section id="aboutCompany" class="w-100 min-vh-100 position-relative d-flex flex-column justify-content-center align-items-center px-2 py-5">

    <div class=" d-flex flex-column justify-content-center align-items-center">
      <h1 class="display-4">About Us</h1>
      <hr class="w-25">
    </div>

    <div class="container gap-5  d-flex flex-column justify-content-center align-items-center">
      <img src="./assets/pexels-cottonbro-5077049.jpg" class="img-fluid rounded col" alt="" style="max-height: 30rem;">
      <div class="col-md-8 aboutText p-3 gap-2">
        <h1 class="mb-3 mb-md-5 ">Our Story</h1>

        <p>Finding the perfect photographer shouldn't be this hard—that's the insight that sparked Aperture. We started as a response to the endless challenges clients face: sifting through portfolios without clear availability, worrying about hidden fees, and struggling to compare styles for events like weddings or corporate gatherings. As a team passionate. </p>

         <p>From our first prototype, we've focused on creating a platform where trusted professionals meet eager clients seamlessly. Today, Aperture connects you with experienced creatives for everything from engagements and birthdays to creative shoots and behind-the-lens sessions, ensuring quality work and on-time delivery every time.
                           </p>



      </div>

      <a href="aboutCompany.php" class="btn bg-light text-dark px-4 rounded border-dark align-self-center">Learn More</a>

    </div>
  </section> -->

  <!------------------------------------- ABOUT COMPANY SECTION-------------------------------------------->




  <!------------------------------------- PRICING SECTION-------------------------------------------->


<!-- 
  <section class="py-5 px-2 bg-light pricing min-vh-100 w-100" id="pricing">
    <div class="container">

      <div class="row justify-content-center align-items-center text-center g-2">
        <div class="col-md-8 mb-4">
          <h1 class="display-4 serif">Pricing</h1>
          <p>Transparent hourly rates tailored to your event needs. Each package includes professional service and high-quality deliverables.</p>
        </div>
      </div>

      <div class="row priceRow justify-content-center align-items-center gap-2">
        <div class="card col-md-4 py-3 px-2 rounded-4 justify-content-center align-items-center border-1 shadow">
          <div class="text-center mb-3">
            <h1 class="card-title">Essential Package</h1>
          </div>

          <div class="price d-flex justify-content-center align-content-center align-items-end gap-2">
            <h1 class="fw-bold text-dark">&#8369;7,500</h1>
          </div>

          <div class="card-body mb-5 p-0">
            <ul class=" list-unstyled">
              <li> <i class="bi bi-check-lg"></i> 3 Hours of Coverage</li>
              <li> <i class="bi bi-check-lg"></i> 40+ Professionally Edited Photos</li>
              <li> <i class="bi bi-check-lg"></i> 1–2 Minute Highlight Video</li>
              <li> <i class="bi bi-check-lg"></i> Basic Color Grading & Audio Sync</li>
              <li> <i class="bi bi-check-lg"></i> Online Gallery Access (1 Month)</li>
            </ul>
          </div>

          <button class="rounded-pill w-75 border-1">Book Now</button>

        </div>

        <div class="card col-md-4 py-3 px-2 rounded-4 justify-content-center align-items-center border-1 shadow">
          <div class="text-center mb-6">
            <h1 class="card-title">Elite Package</h1>
          </div>

          <div class="price d-flex justify-content-center align-content-center align-items-end gap-2">
            <h1 class="fw-bold text-dark">&#8369;15,000</h1>
          </div>

          <div class="card-body mb-5 p-0">
            <ul class=" list-unstyled">
              <li> <i class="bi bi-check-lg"></i> 4 Hours of Coverage</li>
              <li> <i class="bi bi-check-lg"></i> 100+ Professionally Edited Photos</li>
              <li> <i class="bi bi-check-lg"></i> 3–5 Minute Highlight Film</li>
              <li> <i class="bi bi-check-lg"></i> Full Event Video (10–15 minutes)</li>
              <li> <i class="bi bi-check-lg"></i> Drone Coverage (optional, add-on)</li>
              <li> <i class="bi bi-check-lg"></i> Audio Recording for Speeches or Vows</li>
              <li> <i class="bi bi-check-lg"></i> On-Site Lighting Setup</li>
              <li> <i class="bi bi-check-lg"></i> Online Gallery Access (3 Months)</li>
            </ul>


          </div>

          <button class="rounded-pill w-75 border-1">Book Now</button>

        </div>

        <div class="card col-md-4 py-3 px-2 rounded-4 justify-content-center align-items-center border-1 shadow">
          <div class="text-center mb-3">
            <h1 class="card-title">Premium</h1>
          </div>

          <div class="price d-flex justify-content-center align-content-center align-items-end gap-2">
            <h1 class="fw-bold text-dark">&#8369;25,000 </h1>
          </div>

          <div class="card-body mb-5 p-0">
            <ul class="list-unstyled">
              <li> <i class="bi bi-check-lg"></i> 8 Hours of Coverage</li>
              <li> <i class="bi bi-check-lg"></i> Unlimited Raw Photos + 200 Edited Photos</li>
              <li> <i class="bi bi-check-lg"></i> 7–10 Minute Cinematic Highlight Film (Full HD)</li>
              <li> <i class="bi bi-check-lg"></i> Full Event Film (20+ minutes)</li>
              <li> <i class="bi bi-check-lg"></i> Drone Coverage Included</li>
              <li> <i class="bi bi-check-lg"></i> Same-Day Edit (SDE) Included</li>
              <li> <i class="bi bi-check-lg"></i> Audio Mixing & Cinematic Color Grading</li>
              <li> <i class="bi bi-check-lg"></i> Personalized Online Gallery (1 Year Access)</li>
              <li> <i class="bi bi-check-lg"></i> 1 Premium USB Copy with Case</li>
              <li> <i class="bi bi-check-lg"></i> 1 Printed Photo Album (40 Pages)</li>
            </ul>
          </div>

          <button class="rounded-pill w-75 border-1">Book Now</button>

        </div>
      </div>

      <div class="row justify-content-center mt-3">
        <div class="col-md-8">
          <p class="text-secondary text-center">* Overtime billed at the same hourly rate. Minimum booking of 1 hour required.</p>
        </div>
      </div>

    </div>
  </section> -->



  <!------------------------------------- PRICING SECTION-------------------------------------------->

  <section class="w-100  py-5 px-2 bg-white d-flex justify-content-center align-content-center position-relative">
    <div class="container m-0">
      <div class="row justify-content-center mb-5">
        <div class="col-md-10">
          <h1 class="display-4 text-center serif">What Our Clients Say</h1>
          <p class="text-center">Real stories from users who've trusted Aperture for their moments—proving our commitment to seamless bookings and stunning results.</p>
        </div>
      </div>

      <div class="row justify-content-center g-3" id="testimonialRow">
        <div class="col col-md-4">
          <div class="card">
            <div class="card-header d-flex justify-content-center align-items-center gap-2">
              <img src="./assets/pic.png" alt="" class="img-thumbnail rounded-circle" style="max-width: 6rem;">
              <div>
                <h5>Prince Andrew Casiano</h5>
                <small class="text-secondary">CEO, Unishare</small>
              </div>

            </div>
            <div class="card-body">
              <small class="text-secondary">"For our company launch, Aperture's style filters helped us pick the perfect videographer quickly. No hidden fees, and the Premium coverage (&#8369;1000/hr) delivered polished videos in a week—our team loved the quality and ease!"</small>
            </div>
          </div>
        </div>
        <div class="col col-md-4">
          <div class="card">
            <div class="card-header d-flex justify-content-center align-items-center gap-2">
              <img src="./assets/conosido.png" alt="" class="img-thumbnail rounded-circle" style="max-width: 6rem;">
              <div>
                <h5>Dionilo Conosido</h5>
                <small class="text-secondary">Client from Manila</small>
              </div>

            </div>
            <div class="card-body">
              <small class="text-secondary">"Booking for my son's birthday was effortless—Aperture's 4-step process confirmed everything in hours. The Basic package (&#8369;2000/hr) captured all the fun moments with quick edits. Affordable and reliable for family events!"</small>
            </div>
          </div>
        </div>
        <div class="col col-md-4">
          <div class="card">
            <div class="card-header d-flex justify-content-center align-items-center gap-2">
              <img src="./assets/centino.png" alt="" class="img-thumbnail rounded-circle " style="max-width: 6rem;">
              <div>
                <h5>Mark Anthony Centino</h5>
                <small class="text-secondary">Client from Quezon City</small>
              </div>

            </div>
            <div class="card-body">
              <small class="text-secondary">"Aperture made booking a behind-the-lens session straightforward—no availability hassles or extra costs. The Premium pro (&#8369;1000/hr) captured my portfolio perfectly with advanced edits. Trustworthy platform for aspiring creatives."</small>
            </div>
          </div>
        </div>
      </div>





    </div>

  </section>

  <!------------------------------------- FAQ SECTION-------------------------------------------->

  <section class="w-100 py-5 min-vh-100" id="faq">

    <div class="container">

      <div class="row justify-content-center mb-5">
        <div class="col-md-10">
          <h1 class="m-0 text-center display-4 serif">Frequently Asked Questions</h1>
          <p class="text-center">Find answers to common questions about our photography and videography services, pricing, and booking process. We're here to assist you with any additional inquiries.</p>
        </div>
      </div>

      <div class="row justify-content-center">



        <div class="accordion col-md-8" id="faqAccordion">



          <!-- item 1 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#firstQuestion" aria-expanded="true" aria-controls="firstQuestion">
                What is included in the hourly rate?
              </button>
            </h2>

            <div id="firstQuestion" class="accordion-collapse collapse  show" data-bs-parent="#faqAccordion">
              <div class="accordion-body ">
                <p>The hourly rate includes professional photography or videography services, post-production editing, and delivery of high-resolution images or videos. Specific inclusions vary by package.</p>
              </div>
            </div>

          </div>

          <!-- item 2 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#secondQuestion" aria-expanded="false" aria-controls="secondQuestion">
                Is there a minimum booking time?
              </button>
            </h2>

            <div id="secondQuestion" class="accordion-collapse collapse " data-bs-parent="#faqAccordion">
              <div class="accordion-body ">
                <p>Yes, the minimum booking is 1 hour to ensure quality and proper scheduling.</p>
              </div>
            </div>
          </div>

          <!-- item 3 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#thirdQuestion" aria-expanded="false" aria-controls="thirdQuestion">
                How do I book a session?
              </button>
            </h2>

            <div id="thirdQuestion" class="accordion-collapse collapse " data-bs-parent="#faqAccordion">
              <div class="accordion-body ">
                <p>You can book a session by clicking the “Book Now” button on your chosen package or contacting us directly through our website or phone.</p>
              </div>
            </div>
          </div>

          <!-- item 4 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fourthQuestion" aria-expanded="false" aria-controls="fourthQuestion">
                What is the turnaround time for receiving photos/videos?
              </button>
            </h2>

            <div id="fourthQuestion" class="accordion-collapse collapse " data-bs-parent="#faqAccordion">
              <div class="accordion-body ">
                <p>Typically, you will receive your edited photos or videos within 1 to 2 weeks after the event. Express delivery options are available with the Premium package.</p>
              </div>
            </div>
          </div>

          <!-- item 5 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fifthQuestion" aria-expanded="false" aria-controls="fifthQuestion">
                Can I customize a package?
              </button>
            </h2>

            <div id="fifthQuestion" class="accordion-collapse collapse " data-bs-parent="#faqAccordion">
              <div class="accordion-body ">
                <p>Absolutely! We offer custom packages tailored to your specific needs. Please contact us to discuss your requirements.</p>
              </div>
            </div>
          </div>

          <!-- item 6 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sixthQuestion" aria-expanded="false" aria-controls="sixthQuestion">
                Are travel expenses included in the price?
              </button>
            </h2>

            <div id="sixthQuestion" class="accordion-collapse collapse " data-bs-parent="#faqAccordion">
              <div class="accordion-body ">
                <p>All pricing is transparent and upfront. Travel expenses are clearly communicated and included in your quote before booking for events outside our standard service area. There are no hidden fees.</p>
              </div>
            </div>

          </div>

          <!-- item 7 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#seventhQuestion" aria-expanded="false" aria-controls="seventhQuestion">
                What happens if the event runs longer than expected?
              </button>
            </h2>

            <div id="seventhQuestion" class="accordion-collapse collapse " data-bs-parent="#faqAccordion">
              <div class="accordion-body ">
                <p>Overtime is billed at the same hourly rate as your package. We will notify you in advance if additional time is needed.</p>
              </div>
            </div>

          </div>

          <!-- item 8 -->
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#eighthQuestion" aria-expanded="false" aria-controls="eighthQuestion">
                What is your cancellation policy?
              </button>
            </h2>

            <div id="eighthQuestion" class="accordion-collapse collapse " data-bs-parent="#faqAccordion">
              <div class="accordion-body ">
                <p>Cancellations made at least 7 days before the event will receive a full refund. Cancellations within 7 days may be subject to a cancellation fee.</p>
              </div>
            </div>
          </div>

          <!-- item 9 -->
          <div class="accordion-item">

            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ninthQuestion" aria-expanded="false" aria-controls="ninthQuestion">
                Do you provide raw/unedited photos or videos?
              </button>
            </h2>

            <div id="ninthQuestion" class="accordion-collapse collapse " data-bs-parent="#faqAccordion">
              <div class="accordion-body ">
                <p>We deliver professionally edited and color-corrected images and videos. Raw files are not typically provided but can be requested for an additional fee.</p>
              </div>
            </div>

          </div>

          <!-- item 10 -->
          <div class="accordion-item">

            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tenthQuestion" aria-expanded="false" aria-controls="tenthQuestion">
                Can I request specific shots or styles?
              </button>
            </h2>

            <div id="tenthQuestion" class="accordion-collapse collapse " data-bs-parent="#faqAccordion">
              <div class="accordion-body ">
                <p>Yes! We encourage you to share your preferences and ideas during the pre-event consultation to ensure your vision is captured.</p>
              </div>
            </div>

          </div>

        </div>
      </div>
    </div>
  </section>

  <!------------------------------------- FAQ SECTION-------------------------------------------->


  <?php include './includes/footer.php'; ?>

  <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
</body>

</html>
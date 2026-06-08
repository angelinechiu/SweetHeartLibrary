<template>
  <footer class="py-5 mt-auto" style="background-color: #2C2C2C; color: #F8F4F0; border-top: 1px solid #3A3A3A;">
    <div class="container">
      <div class="row gy-4">

        
        <div class="col-lg-5">
          <div class="d-flex align-items-center mb-3">
            <img src="../assets/images/logo.png" alt="Logo" style="width: 42px; height: 42px; margin-right: 12px;">
            <span class="fw-bold fs-5" style="color: #E8B4B8;">Sweetheart Library</span>
          </div>
          <p class="mb-2" style="color: #D9CFC2; font-size: 0.95rem;">
            A refined space for knowledge, inspiration, and quiet elegance.
          </p>
        </div>

        
        <div class="col-lg-4">
          <h6 class="fw-semibold mb-3" style="color: #E8B4B8;">Contact Us</h6>
          <ul class="list-unstyled small" style="color: #D9CFC2; line-height: 1.8;">
            <li><strong>Address:</strong><br> Level 3, Sweetheart Building,<br> 88 Jalan Elegance, 50450 Kuala Lumpur</li>
            <li class="mt-2"><strong>Tel:</strong> +603 2282 4455</li>
            <li><strong>Email:</strong> <a href="mailto:feedback@sweetheartlibrary.com" class="text-decoration-none" style="color: #E8B4B8;">feedback@sweetheartlibrary.com</a></li>
          </ul>
        </div>

        
        <div class="col-lg-3">
          <h6 class="fw-semibold mb-3" style="color: #E8B4B8;">Opening Hours</h6>
          <p class="small mb-1" style="color: #D9CFC2;">
            Monday – Saturday<br>
            <strong>9:00 AM – 11:00 PM</strong>
          </p>
          <p class="small mb-3" style="color: #D9CFC2;">
            <strong>Closed</strong> on Sundays &amp; Public Holidays
          </p>

          <router-link
            to="/feedback"
            class="btn btn-sm px-4 py-2"
            style="background-color: #E8B4B8; color: #2C2C2C; border-radius: 30px; font-size: 0.85rem; font-weight: 600;"
          >
            Send Feedback
          </router-link>
        </div>

      </div>

      <hr class="my-4" style="border-color: #3A3A3A;">

      <div class="text-center">
        <small style="color: #D9CFC2;">
          © 2026 Sweetheart Library. All Rights Reserved. • Crafted with elegance.
        </small>
      </div>
    </div>
  </footer>
</template>

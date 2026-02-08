@extends('layouts.app')

@section('content')

<div class="contact-hero text-center" style="margin-bottom: 3rem;">
    <div class="container">
        <h1 class="fw-bold display-5">Hubungi Kami</h1>
        <p class="lead mb-0 opacity-75">Kami sedia membantu urusan pendaftaran & semakan plot kubur.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4 align-items-stretch">
        
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden">
                <div class="card-body p-4 p-lg-5">
                    <h3 class="fw-bold text-success mb-4">Maklumat Pejabat</h3>
                    <p class="text-muted mb-5">
                        Sila lawati pejabat pengurusan kami bagi sebarang urusan rasmi atau hubungi talian di bawah.
                    </p>
                    
                    <div class="d-flex mb-4 align-items-start">
                        <div class="contact-icon-box shadow-sm">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1 text-dark">Alamat Operasi</h6>
                            <p class="text-muted mb-0 small">
                                Tanah Perkuburan Islam Raudhatul Sa’adah,<br>
                                Kampung Johan Setia, 41200 Klang, Selangor.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex mb-4 align-items-start">
                        <div class="contact-icon-box shadow-sm">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1 text-dark">Telefon</h6>
                            <p class="text-muted mb-0 fw-bold">+60 3-3323 1234</p>
                            <small class="text-success">Talian Pejabat (9am - 5pm)</small>
                        </div>
                    </div>

                    <div class="d-flex mb-4 align-items-start">
                        <div class="contact-icon-box shadow-sm">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1 text-dark">Emel Rasmi</h6>
                            <p class="text-muted mb-0 small">admin@tpirs.gov.my</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <div class="contact-icon-box shadow-sm">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-1 text-dark">Waktu Operasi</h6>
                            <p class="text-muted mb-0 small">Isnin - Ahad: 8:00 Pagi - 6:00 Petang</p>
                        </div>
                    </div>

                </div>
                <div class="position-absolute bottom-0 end-0 opacity-10 p-3">
                    <i class="fas fa-mosque fa-5x text-success"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.346576856086!2d101.48866531475704!3d2.973715997833989!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cdac3090555555%3A0x1234567890abcdef!2sKampung%20Johan%20Setia!5e0!3m2!1sen!2smy!4v1625000000000!5m2!1sen!2smy" 
                    width="100%" 
                    height="100%" 
                    style="border:0; min-height: 450px;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
                
                <div class="card-footer bg-white border-0 p-3 text-end">
                    <a href="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.480584137241!2d101.5057760744704!3d2.9640672542635604!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cdad4d8b727d13%3A0x5883c6f802b79617!2sTanah%20Perkuburan%20Islam%20Raudhatul%20Sa&#39;adah%20Kampung%20Johan%20Setia%20(TPIRS)!5e0!3m2!1sen!2smy!4v1768927347997!5m2!1sen!2smy" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-4">
                        <i class="fas fa-external-link-alt me-2"></i>Buka di Google Maps
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
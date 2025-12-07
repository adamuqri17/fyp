@extends('layouts.app')

@section('content')

<div class="contact-hero text-center">
    <div class="container">
        <h1 class="fw-bold display-5">Hubungi Kami</h1>
        <p class="lead mb-0 opacity-75">Kami sedia membantu anda dengan sebarang pertanyaan mengenai plot kubur.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-5">
        
        <div class="col-lg-5">
            <div class="pe-lg-4">
                <h3 class="fw-bold text-success mb-4">Maklumat Pejabat</h3>
                <p class="text-muted mb-4">
                    Sila hubungi kami atau lawati pejabat pengurusan kami bagi urusan pendaftaran, semakan status plot, atau aduan penyelenggaraan.
                </p>
                
                <div class="d-flex mb-4">
                    <div class="contact-icon-box">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="fw-bold mb-1">Alamat Operasi</h6>
                        <p class="text-muted mb-0">
                            Tanah Perkuburan Islam Raudhatul Sa’adah,<br>
                            Kampung Johan Setia, 41200 Klang, Selangor.
                        </p>
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <div class="contact-icon-box">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="fw-bold mb-1">Telefon</h6>
                        <p class="text-muted mb-0">+60 3-3323 1234</p>
                        <small class="text-success fw-bold">Isnin - Jumaat, 9:00 AM - 5:00 PM</small>
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <div class="contact-icon-box">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="fw-bold mb-1">Emel Rasmi</h6>
                        <p class="text-muted mb-0">admin@tpirs.gov.my</p>
                    </div>
                </div>

                <div class="rounded-3 overflow-hidden shadow-sm mt-4 border">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.146637315726!2d101.48529231475704!3d3.055403997774786!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc536767666667%3A0x6666666666666666!2sKampung%20Johan%20Setia%2C%20Klang%2C%20Selangor!5e0!3m2!1sen!2smy!4v1620000000000!5m2!1sen!2smy" 
                        width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <h3 class="fw-bold mb-2">Hantar Mesej</h3>
                <p class="text-muted mb-4">Isi borang di bawah untuk menghantar pertanyaan terus kepada kami.</p>
                
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">NAMA PENUH</label>
                            <input type="text" class="form-control form-control-soft" placeholder="Masukkan nama anda">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">EMEL</label>
                            <input type="email" class="form-control form-control-soft" placeholder="nama@contoh.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">SUBJEK</label>
                            <input type="text" class="form-control form-control-soft" placeholder="Tajuk pertanyaan">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">MESEJ</label>
                            <textarea class="form-control form-control-soft" rows="5" placeholder="Tulis mesej anda di sini..."></textarea>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="button" class="btn btn-success w-100 py-3 fw-bold shadow-sm hover-elevate transition-all">
                                <i class="fas fa-paper-plane me-2"></i> Hantar Mesej
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
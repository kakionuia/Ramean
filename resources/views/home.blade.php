<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard · Washly</title>
    <link rel="stylesheet" href="{{ asset('user/user.css') }}">
    <style>
        :root{--dash-ink:#172033;--dash-muted:#58646a;--dash-teal:#00796f;--dash-line:#e3e7ff;--dash-bg:#faf8ff;--dash-lilac:#f1f1ff}
        .dash-greeting{position:relative;min-height:275px;padding:36px 38px;border:0;border-radius:24px;overflow:hidden;color:#fff;background:linear-gradient(118deg,#007e78 0%,#078f9b 52%,#267fc6 100%);box-shadow:0 16px 35px #087f8b26}
        .dash-greeting:after{position:absolute;right:7%;bottom:-100px;width:320px;height:320px;border:1px solid #ffffff30;border-radius:50%;box-shadow:0 0 0 32px #ffffff0c,0 0 0 66px #ffffff0a;content:""}
        .dash-greeting>div{position:relative;z-index:1;max-width:680px}
        .dash-greeting .member-pill{color:#eaffff;background:#ffffff24}
        .dash-greeting .member-pill:before{background:#75f1d8}
        .dash-greeting h1{font-size:clamp(32px,4vw,46px)}
        .dash-greeting p{max-width:580px;color:#e0f7fa}
        .dash-greeting .dash-cta{position:relative;z-index:2;color:#087773;background:#fff;box-shadow:0 8px 18px #003f4033}
        .dash-greeting .dash-cta:before{content:"↗";border:0;font-size:22px}
        .dash-greeting .dash-cta:before{content:"\1F6F5";border:0;font-size:26px}
        .hero-art{position:absolute;right:42px;bottom:-7px;z-index:1;width:330px;max-width:34%;filter:drop-shadow(0 16px 15px #00485835);animation:heroBob 4s ease-in-out infinite}
        .hero-art svg{display:block;width:100%;height:auto;overflow:visible}
        @keyframes heroBob{0%,100%{transform:translateY(0)}50%{transform:translateY(-7px)}}
        .order-focus{box-shadow:0 12px 30px #252d3810}
        .stat-card{box-shadow:0 10px 24px #252d3810;transition:transform .2s ease,box-shadow .2s ease}
        .stat-card:hover{transform:translateY(-3px);box-shadow:0 15px 30px #252d3818}
        .stat-card:first-child{border-color:#c7ece7}.stat-card:nth-child(2){border-color:#cfe7ff}.stat-card:nth-child(3){border-color:#c8f1e9}
        .order-steps{grid-template-columns:repeat(3,1fr);gap:0}
        .step:not(:last-child):after{top:20px;left:calc(50% + 28px);width:calc(100% - 56px);height:3px;background:#e8eafd}
        .step.done:not(:last-child):after{background:#00a992}
        .step.current:not(:last-child):after{background:linear-gradient(90deg,#00a992 0 45%,#e8eafd 45%)}
        .step-mark{width:44px;height:44px}
        .step-empty{display:flex;min-height:230px;align-items:center;justify-content:center;flex-direction:column;padding:28px 20px}
        .empty-machine{position:relative;display:grid;width:92px;height:104px;place-items:center;margin-bottom:14px;border:5px solid #c8efea;border-radius:25px;background:linear-gradient(145deg,#f5fffe,#dff5f3);animation:machineFloat 3s ease-in-out infinite}
        .empty-machine:before{position:absolute;top:9px;width:12px;height:4px;border-radius:4px;background:#34b8a7;box-shadow:18px 0 #c0e8e3;content:""}
        .empty-machine:after{width:47px;height:47px;border:6px solid #63cfc0;border-radius:50%;background:radial-gradient(circle at 35% 30%,#fff 0 9%,#b4ece4 10% 32%,#47bfb1 33% 100%);content:""}
        @keyframes machineFloat{0%,100%{transform:translateY(0) rotate(-1deg)}50%{transform:translateY(-7px) rotate(1deg)}}
        .empty-cta{display:inline-flex;margin-top:13px;padding:11px 16px;border-radius:11px;color:white;background:var(--dash-teal);font-weight:700}
        .lower-grid{display:grid;grid-template-columns:minmax(0,1fr) 285px;align-items:start;gap:22px;margin-top:46px}
        .lower-grid .recent-section{min-width:0;margin-top:0}
        .tips-card{padding:23px;border:1px solid #cce9e9;border-radius:19px;background:linear-gradient(150deg,#eefcfc,#f2f4ff);box-shadow:0 10px 24px #252d3810}
        .tips-label{display:inline-flex;padding:6px 10px;border-radius:20px;color:#087d78;background:#d8f3ef;font-size:12px;font-weight:750}
        .tips-card h2{margin:16px 0 8px;font-size:20px;line-height:1.25}
        .tips-card p{margin:0;color:var(--dash-muted);line-height:1.6}
        .tips-card strong{display:block;margin-top:17px;color:var(--dash-teal);font-size:17px}
        .support-banner{box-shadow:0 10px 24px #252d380c}
        @media(max-width:900px){.lower-grid{grid-template-columns:1fr}.tips-card{order:2}}
        @media(max-width:560px){.dash-greeting{min-height:310px;align-items:flex-start;padding:25px 22px}.dash-greeting .dash-cta{margin-top:10px}.dash-greeting:after{right:-95px;bottom:-125px;width:260px;height:260px}.hero-art{right:4px;bottom:-18px;width:145px;max-width:40%;opacity:.82}.order-steps{grid-template-columns:repeat(3,minmax(0,1fr))}}
        .dash-notice,.focus-icon,.step-mark,.stat-icon,.support-icon,.dash-cta{font-size:0!important}
        .dash-cta{font-size:16px!important}
        .dash-cta span{display:none}
        .dash-notice:before{content:"\1F514";font-size:22px;line-height:1}
        .dash-cta:before{content:"+";display:grid;width:21px;height:21px;place-items:center;border:2px solid currentColor;border-radius:50%;font-size:20px;line-height:1}
        .focus-icon:before,.stat-card:first-child .stat-icon:before{content:"\1F9FA";font-size:25px;line-height:1}
        .step-mark:before{font-size:19px;line-height:1}
        .step.done .step-mark:before{content:"\2713"}
        .step.current .step-mark:before{content:"\25B6";font-size:13px}
        .stat-card:nth-child(2) .stat-icon:before{content:"\1F9F9";font-size:24px}
        .stat-card:nth-child(3) .stat-icon:before{content:"\2713";font-size:29px;font-weight:700}
        .support-icon:before{content:"\1F3A7";font-size:25px}
        body.customer-page{background:var(--dash-bg);color:var(--dash-ink);font-family:Inter,"Segoe UI",Arial,sans-serif}
        .dash-nav{position:sticky;top:0;z-index:20;background:#fff;border-bottom:1px solid #f0eff8}
        .dash-nav-inner{width:min(100% - 48px,1320px);min-height:76px;margin:auto;display:flex;align-items:center;gap:34px}
        .dash-brand{display:flex;align-items:center;gap:10px;color:var(--dash-teal);font-size:25px;font-weight:750;letter-spacing:-1px}
        .dash-brand-mark{display:block;width:46px;height:46px;object-fit:contain}
        .dash-tabs{display:flex;gap:5px;padding:5px;border-radius:16px;background:var(--dash-lilac)}
        .dash-tabs a{padding:10px 20px;border-radius:12px;color:#4e5a5b;font-weight:650;white-space:nowrap}
        .dash-tabs a.active{color:white;background:#006f66}
        .dash-actions{margin-left:auto;display:flex;align-items:center;gap:22px}
        .dash-notice{position:relative;border:0;background:transparent;color:#263746;font-size:23px;cursor:pointer}
        .dash-notice i{position:absolute;top:3px;right:1px;width:9px;height:9px;border-radius:50%;background:var(--dash-teal)}
        .dash-account{display:flex;align-items:center;gap:11px;font-size:15px;font-weight:650}
        .dash-account .user-avatar{width:42px;height:42px;color:white;background:var(--dash-teal);font-size:15px}
        .dash-account .nav-user-copy small{display:none}
        .dash-main{width:min(100% - 48px,1320px);padding:38px 0 30px;margin:auto}
        .dash-greeting{display:flex;justify-content:space-between;align-items:center;gap:20px;padding:0 0 30px;border-bottom:1px solid var(--dash-line)}
        .member-pill{display:inline-flex;align-items:center;gap:7px;padding:6px 12px;border-radius:20px;background:#eeefff;color:var(--dash-teal);font-weight:650}
        .member-pill:before{content:"";width:8px;height:8px;border-radius:50%;background:var(--dash-teal)}
        .dash-greeting h1{margin:12px 0 5px;font-size:clamp(28px,4vw,42px);letter-spacing:-1.4px}
        .dash-greeting p{margin:0;color:var(--dash-muted);font-size:17px}
        .dash-cta{display:inline-flex;align-items:center;gap:10px;padding:15px 24px;border-radius:15px;color:white;background:var(--dash-teal);font-weight:700;white-space:nowrap}
        .dash-cta:hover{background:#00645d}
        .order-focus{margin-top:38px;padding:28px 30px 24px;border:1px solid #f0eff8;border-radius:22px;background:white;box-shadow:0 2px 5px #1d294008}
        .focus-head{display:flex;justify-content:space-between;align-items:center;gap:18px}
        .focus-title{display:flex;align-items:center;gap:13px}
        .focus-icon{display:grid;place-items:center;width:54px;height:54px;border-radius:16px;color:var(--dash-teal);background:#e6f4f2;font-size:25px}
        .focus-title h2{display:flex;align-items:center;gap:10px;margin:0 0 5px;font-size:18px}
        .focus-title p{margin:0;color:var(--dash-muted);font-size:15px}
        .status-tag{padding:8px 12px;border-radius:20px;color:#0873a8;background:#d8efff;font-size:13px;font-weight:700}
        .text-link{color:var(--dash-teal);font-weight:700;white-space:nowrap}
        .order-steps{display:grid;grid-template-columns:repeat(5,1fr);margin-top:27px}
        .step{position:relative;display:flex;align-items:center;flex-direction:column;gap:7px;color:#87918f;font-weight:650;text-align:center}
        .step:not(:last-child):after{position:absolute;z-index:0;top:19px;left:calc(50% + 23px);width:calc(100% - 46px);height:2px;background:#e8eafd;content:""}
        .step-mark{z-index:1;display:grid;place-items:center;width:40px;height:40px;border-radius:50%;background:#eff0ff;font-size:18px}
        .step.done,.step.current{color:var(--dash-teal)}
        .step.done .step-mark{color:white;background:var(--dash-teal)}
        .step.current .step-mark{outline:5px solid #def0ed;color:white;background:#008c80}
        .step-empty{padding:20px 0;text-align:center;color:#7a8589}
        .dash-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:38px}
        .stat-card{display:flex;justify-content:space-between;align-items:center;padding:20px 22px;border:1px solid var(--dash-line);border-radius:16px;background:#fff}
        .stat-card p{margin:0 0 8px;color:var(--dash-muted);font-size:14px}
        .stat-card strong{font-size:29px;letter-spacing:-.8px}
        .stat-card strong small{font-size:16px;font-weight:500;letter-spacing:0}
        .stat-icon{display:grid;place-items:center;width:50px;height:50px;border-radius:13px;color:var(--dash-teal);background:#e7f4f2;font-size:23px}
        .stat-card:nth-child(2) strong{color:#0875a8}.stat-card:nth-child(2) .stat-icon{color:#0875a8;background:#e4f2ff}.stat-card:nth-child(3) strong{color:var(--dash-teal)}.stat-card:nth-child(3) .stat-icon{background:#d9faf5}
        .recent-section{margin-top:46px}
        .recent-head{display:flex;justify-content:space-between;align-items:end;margin-bottom:13px}
        .recent-head h2{margin:0 0 4px;font-size:24px;letter-spacing:-.5px}
        .recent-head p{margin:0;color:var(--dash-muted);font-size:15px}
        .orders-wrap{overflow-x:auto;border:1px solid #f0eff8;border-radius:19px;background:white;box-shadow:0 2px 5px #1d294008}
        .orders-table{width:100%;border-collapse:collapse;text-align:left;white-space:nowrap}
        .orders-table th{padding:17px 20px;background:var(--dash-lilac);color:#576269;font-size:12px;letter-spacing:.04em}
        .orders-table td{padding:17px 20px;border-top:1px solid #eef0fa;color:#38444c}
        .order-number{color:var(--dash-teal);font-weight:750}.service-pill{padding:6px 10px;border-radius:8px;background:#eff0ff;font-weight:650}
        .price-cell{color:var(--dash-ink)!important;font-size:16px;font-weight:700}
        .table-status{display:inline-flex;align-items:center;gap:7px;padding:7px 11px;border-radius:18px;color:#08638d;background:#dcefff;font-weight:700}
        .table-status:before{width:7px;height:7px;border-radius:50%;background:currentColor;content:""}
        .table-status.completed{color:#00796f;background:#d9faf5}.table-status.cancelled{color:#9b4b49;background:#fff0ed}
        .detail-button{padding:9px 14px;border-radius:10px;color:var(--dash-teal);background:#f1f1ff;font-weight:700}
        .table-empty{padding:38px 20px!important;color:#738080!important;text-align:center}
        .support-banner{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-top:38px;padding:25px 27px;border-radius:20px;background:#f0f1ff}
        .support-copy{display:flex;align-items:center;gap:16px}.support-copy .support-icon{display:grid;place-items:center;width:54px;height:54px;flex:none;border-radius:50%;color:var(--dash-teal);background:white;font-size:23px}
        .support-copy h2{margin:0 0 4px;font-size:19px}.support-copy p{margin:0;color:var(--dash-muted);font-size:15px}
        .support-link{padding:13px 18px;border-radius:12px;background:white;font-weight:700;white-space:nowrap}
        .dash-footer{background:#fff}.dash-footer-inner{display:flex;align-items:center;gap:15px;width:min(100% - 48px,1320px);min-height:75px;margin:auto;color:#596568}
        .dash-footer .dash-brand{font-size:22px}.dash-footer .dash-brand-mark{width:35px;height:38px}.dash-footer form{margin-left:auto}.dash-footer button{border:0;color:inherit;background:none;font:inherit;cursor:pointer}
        @media(max-width:800px){.dash-nav-inner{flex-wrap:wrap;gap:12px;padding:12px 0}.dash-tabs{order:3;width:100%;overflow:auto}.dash-tabs a{padding:9px 13px}.dash-actions{gap:12px}.dash-main{padding-top:25px}.dash-greeting{align-items:flex-start;flex-direction:column}.order-focus{padding:20px}.dash-stats{gap:10px}.stat-card{padding:16px}.stat-icon{width:42px;height:42px}.support-banner{align-items:flex-start;flex-direction:column}}
        @media(max-width:560px){.dash-nav-inner,.dash-main,.dash-footer-inner{width:calc(100% - 30px)}.dash-brand{font-size:21px}.dash-brand-mark{width:39px;height:39px}.dash-account .nav-user-copy strong{max-width:90px}.dash-greeting p{font-size:15px}.dash-cta{padding:12px 17px}.focus-head{align-items:flex-start;flex-direction:column}.focus-title{align-items:flex-start}.focus-title h2{align-items:flex-start;flex-direction:column}.order-steps{margin-top:24px}.step{font-size:11px}.step-mark{width:34px;height:34px}.step:not(:last-child):after{top:16px;left:calc(50% + 20px);width:calc(100% - 40px)}.dash-stats{grid-template-columns:1fr}.recent-head{align-items:flex-start;gap:12px;flex-direction:column}.support-copy{align-items:flex-start}.support-copy p{line-height:1.5}.dash-footer-inner{flex-wrap:wrap;padding:16px 0}.dash-footer form{margin-left:0}}
        body.customer-page .dash-greeting{display:flex;position:relative;min-height:275px;align-items:center;justify-content:space-between;gap:24px;margin:0;padding:36px 38px;border:0;border-radius:24px;overflow:hidden;color:#fff;background:linear-gradient(118deg,#007e78 0%,#078f9b 52%,#267fc6 100%);box-shadow:0 16px 35px #087f8b26}
        body.customer-page .dash-greeting>div:first-child{z-index:2;max-width:610px}
        body.customer-page .dash-greeting:after{position:absolute;right:6%;bottom:-115px;width:320px;height:320px;border:1px solid #ffffff30;border-radius:50%;box-shadow:0 0 0 32px #ffffff0c,0 0 0 66px #ffffff0a;content:""}
        body.customer-page .dash-greeting .member-pill{color:#eaffff;background:#ffffff24}
        body.customer-page .dash-greeting .member-pill:before{background:#75f1d8}
        body.customer-page .dash-greeting h1{font-size:clamp(32px,4vw,46px)}
        body.customer-page .dash-greeting p{max-width:580px;color:#e0f7fa}
        body.customer-page .dash-greeting .dash-cta{z-index:2;flex:none;color:#087773;background:#fff;box-shadow:0 8px 18px #003f4033}
        body.customer-page .dash-greeting .dash-cta:before{content:"\1F6F5";width:auto;height:auto;border:0;font-size:26px}
        body.customer-page .hero-art{position:absolute;right:190px;bottom:-7px;z-index:1;width:295px;max-width:30%;filter:drop-shadow(0 16px 15px #00485835);animation:heroBob 4s ease-in-out infinite}
        body.customer-page .hero-art svg{display:block;width:100%;height:auto}
        body.customer-page .order-steps{grid-template-columns:repeat(3,1fr);gap:0}
        body.customer-page .order-focus{box-shadow:0 12px 30px #252d3810}
        body.customer-page .step:not(:last-child):after{top:20px;left:calc(50% + 28px);width:calc(100% - 56px);height:3px;background:#e8eafd}
        body.customer-page .step.done:not(:last-child):after{background:#00a992}
        body.customer-page .step.current:not(:last-child):after{background:linear-gradient(90deg,#00a992 0 45%,#e8eafd 45%)}
        body.customer-page .step-mark{width:44px;height:44px}
        body.customer-page .focus-icon:before{content:"\1F9FA";font-size:25px}
        body.customer-page .stat-card:first-child .stat-icon:before{content:"\1F455";font-size:25px}
        body.customer-page .stat-card:nth-child(2) .stat-icon:before{content:"\1F9FC";font-size:24px}
        body.customer-page .step-empty{display:flex;min-height:280px;align-items:center;justify-content:center;flex-direction:column;padding:28px 20px}
        body.customer-page .empty-machine{position:relative;display:grid;width:92px;height:104px;place-items:center;margin-bottom:14px;border:5px solid #c8efea;border-radius:25px;background:linear-gradient(145deg,#f5fffe,#dff5f3);animation:machineFloat 3s ease-in-out infinite}
        body.customer-page .empty-machine:before{position:absolute;top:9px;width:12px;height:4px;border-radius:4px;background:#34b8a7;box-shadow:18px 0 #c0e8e3;content:""}
        body.customer-page .empty-machine:after{width:47px;height:47px;border:6px solid #63cfc0;border-radius:50%;background:radial-gradient(circle at 35% 30%,#fff 0 9%,#b4ece4 10% 32%,#47bfb1 33% 100%);content:"";animation:drumSpin 7s linear infinite}
        body.customer-page .stat-card{box-shadow:0 10px 24px #252d3810;transition:transform .2s ease,box-shadow .2s ease}
        body.customer-page .stat-card:hover{transform:translateY(-3px);box-shadow:0 15px 30px #252d3818}
        body.customer-page .stat-card:first-child{border-color:#c7ece7}body.customer-page .stat-card:nth-child(2){border-color:#cfe7ff}body.customer-page .stat-card:nth-child(3){border-color:#c8f1e9}
        body.customer-page .lower-grid{display:grid;grid-template-columns:minmax(0,1fr) 285px;align-items:start;gap:22px;margin-top:46px}
        body.customer-page .lower-grid .recent-section{min-width:0;margin-top:0}
        body.customer-page .tips-card{padding:23px;border:1px solid #cce9e9;border-radius:19px;background:linear-gradient(150deg,#eefcfc,#f2f4ff);box-shadow:0 10px 24px #252d3810}
        body.customer-page .tips-label{display:inline-flex;padding:6px 10px;border-radius:20px;color:#087d78;background:#d8f3ef;font-size:12px;font-weight:750}
        body.customer-page .tips-card h2{margin:16px 0 8px;font-size:20px;line-height:1.25}
        body.customer-page .tips-card p{margin:0;color:var(--dash-muted);line-height:1.6}
        body.customer-page .tips-card strong{display:block;margin-top:17px;color:var(--dash-teal);font-size:17px}
        body.customer-page .support-banner{box-shadow:0 10px 24px #252d3810}
        @keyframes heroBob{0%,100%{transform:translateY(0)}50%{transform:translateY(-7px)}}
        @keyframes machineFloat{0%,100%{transform:translateY(0) rotate(-1deg)}50%{transform:translateY(-7px) rotate(1deg)}}
        @keyframes drumSpin{to{transform:rotate(360deg)}}
        @media(max-width:900px){body.customer-page .lower-grid{grid-template-columns:1fr}body.customer-page .tips-card{order:2}}
        @media(max-width:800px){body.customer-page .dash-greeting{align-items:flex-start;flex-direction:column}body.customer-page .hero-art{right:28px;width:210px;max-width:30%}}
        @media(max-width:560px){body.customer-page .dash-greeting{min-height:320px;padding:25px 22px}body.customer-page .dash-greeting .dash-cta{margin-top:8px}body.customer-page .dash-greeting:after{right:-95px;bottom:-125px;width:260px;height:260px}body.customer-page .hero-art{right:4px;bottom:-15px;width:145px;max-width:39%;opacity:.84}body.customer-page .order-steps{grid-template-columns:repeat(3,minmax(0,1fr))}}
    </style>
    <link rel="stylesheet" href="{{ asset('css/site-motion.css') }}">
</head>

<body class="customer-page">
    <header class="dash-nav">
        <div class="dash-nav-inner">
            <a class="dash-brand" href="{{ route('dashboard') }}"><img class="dash-brand-mark" src="{{ asset('images/washly-logo.png') }}" alt="">Washly</a>
            <nav class="dash-tabs" aria-label="Navigasi utama">
                <a class="active" href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('laundries.discover') }}">Pesan Laundry</a>
                <a href="{{ route('profile', ['tab' => 'orders']) }}">Riwayat Pesanan</a>
            </nav>
            <div class="dash-actions">
                <div class="notification-wrap">
                    <button class="dash-notice" id="notificationButton" type="button" aria-label="Notifikasi" aria-expanded="false">♧@if ($notifications->isNotEmpty())<i></i>@endif</button>
                    <div class="notification-popover" id="notificationPopover" hidden>
                        <strong>Aktivitas pesanan</strong>
                        @forelse ($notifications as $notification)
                            <a href="{{ route('orders.show', $notification->id) }}"><span>{{ $notification->service_name }}</span><small>{{ ucfirst($notification->status) }} · {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small></a>
                        @empty<p>Belum ada notifikasi.</p>@endforelse
                    </div>
                </div>
                <a class="dash-account" href="{{ route('profile') }}"><span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span class="nav-user-copy"><strong>{{ auth()->user()->name }}</strong></span><span aria-hidden="true">⌄</span></a>
            </div>
        </div>
    </header>
    <main class="dash-main">
        @if (session('success'))<div class="customer-notice">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="customer-notice notice-error">{{ $errors->first() }}</div>@endif
        <section class="dash-greeting">
            <div><span class="member-pill">Member Washly</span><h1>Halo, {{ auth()->user()->name }} 👋</h1><p>Selamat datang kembali di Washly. Pantau cucianmu atau jadwalkan pesanan baru.</p></div>
            <div class="hero-art" aria-hidden="true"><svg viewBox="0 0 320 250" role="presentation"><ellipse cx="169" cy="226" rx="110" ry="15" fill="#005a67" opacity=".22"/><path d="M220 78c34 12 53 45 49 81l-9 47h-74l4-69c1-31 7-52 30-59Z" fill="#f2fbf7"/><path d="M226 83c27 15 28 45 22 69l-9 45h-30l5-63c2-22 0-38 12-51Z" fill="#d8f1ec"/><path d="M209 125c-22 13-36 17-58 15l-37-4c-9-1-12 9-4 13l37 17c19 9 39 8 56-1l24-14" fill="#ffd2a2" stroke="#eaa66f" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/><path d="M120 148c-20-7-39-22-51-36-6-7-17 0-12 9 11 21 28 39 50 50l28 13" fill="#ffd2a2" stroke="#eaa66f" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/><path d="M100 170h111l-11 48h-91l-9-48Z" fill="#efa76b"/><path d="M109 175h94l-8 36h-79l-7-36Z" fill="#f7c489"/><path d="M111 171c4-14 13-20 25-17 9-13 23-13 32-3 12-6 23 1 25 20" fill="#fff"/><path d="M131 155c1-11 6-18 15-18 8 0 13 8 12 18m11-3c3-11 10-15 17-12 7 3 8 9 5 17" fill="#82d3c5"/><path d="M96 171h119" stroke="#d78950" stroke-width="6" stroke-linecap="round"/><path d="M128 218v7m53-7v7" stroke="#eaa66f" stroke-width="6" stroke-linecap="round"/><circle cx="166" cy="61" r="33" fill="#ffd2a2"/><path d="M134 60c-4-24 10-42 31-43 21-1 34 14 34 35-9-4-16-11-20-19-11 13-25 20-45 21Z" fill="#263445"/><path d="M144 68c4 3 8 3 12 0m15 0c4 3 8 3 12 0" stroke="#5a3930" stroke-width="3" stroke-linecap="round"/><path d="M157 79c6 5 13 5 19 0" stroke="#c86855" stroke-width="3" stroke-linecap="round"/><path d="M145 95c-10 7-20 16-25 31l-6 24 33 5 13-34 27 33 25-16-24-34c-12-15-30-18-43-9Z" fill="#fffbeb"/><circle cx="87" cy="62" r="5" fill="#a3f2dc"/><circle cx="258" cy="43" r="7" fill="#ffe08f"/><path d="m67 83 7 6-7 6-7-6 7-6Z" fill="#d1fbf2"/></svg></div>
            <a class="dash-cta" href="{{ route('laundries.discover') }}"><span aria-hidden="true">⊕</span> Pesan Laundry</a>
        </section>
        <section class="order-focus" aria-label="Pesanan yang sedang berjalan">
            @if ($activeOrder)
                @php
                    $currentStep = $activeOrder->status === 'completed' ? 3 : (($activeOrder->weighed_at || $activeOrder->status === 'processing' || in_array($activeOrder->delivery_status, ['picked_up', 'received'], true)) ? 2 : 1);
                    $progressSteps = ['Kurir Jemput', 'Proses Cuci', 'Kurir Antar'];
                    $statusLabel = $activeOrder->status === 'processing' ? 'Diproses' : ($activeOrder->status === 'completed' ? 'Selesai' : ucfirst($activeOrder->status));
                @endphp
                <div class="focus-head"><div class="focus-title"><span class="focus-icon" aria-hidden="true">▣</span><div><h2>{{ $activeOrder->status === 'completed' ? 'Pesanan Terakhir' : 'Pesanan Berjalan' }}: #WSH-{{ str_pad((string) $activeOrder->id, 6, '0', STR_PAD_LEFT) }} <span class="status-tag">{{ $statusLabel }}</span></h2><p>{{ $activeOrder->service_name }} · {{ $activeOrder->store_name }}</p></div></div><a class="text-link" href="{{ route('orders.show', $activeOrder->id) }}">Lacak Detail Pesanan →</a></div>
                <div class="order-steps">@foreach ($progressSteps as $index => $step)<div class="step {{ $index + 1 < $currentStep || ($activeOrder->status === 'completed' && $activeOrder->delivery_status === 'returned') ? 'done' : '' }} {{ $index + 1 === $currentStep && !($activeOrder->status === 'completed' && $activeOrder->delivery_status === 'returned') ? 'current' : '' }}"><span class="step-mark">{{ $index + 1 < $currentStep ? '✓' : ($index + 1 === $currentStep ? '•' : '·') }}</span><span>{{ $step }}</span></div>@endforeach</div>
            @else
                <div class="step-empty"><div class="empty-machine" aria-hidden="true"></div><h2>Belum ada pesanan berjalan</h2><p>Pakaian kotor menumpuk? Yuk, jadwalkan jemputan pertamamu sekarang!</p><a class="empty-cta" href="{{ route('laundries.discover') }}">Mulai Pesan</a></div>
            @endif
        </section>
        <section class="dash-stats" aria-label="Ringkasan pesanan">
            <article class="stat-card"><div><p>Pesanan Aktif</p><strong>{{ $orderStats->active ?? 0 }} <small>pesanan</small></strong></div><span class="stat-icon" aria-hidden="true">▣</span></article>
            <article class="stat-card"><div><p>Sedang Diproses</p><strong>{{ $orderStats->processing ?? 0 }} <small>pesanan</small></strong></div><span class="stat-icon" aria-hidden="true">⌁</span></article>
            <article class="stat-card"><div><p>Pesanan Selesai</p><strong>{{ $orderStats->completed ?? 0 }} <small>transaksi</small></strong></div><span class="stat-icon" aria-hidden="true">✓</span></article>
        </section>
        <div class="lower-grid">
        <section class="recent-section" id="pesan">
            <div class="recent-head"><div><h2>Pesanan Terakhir</h2><p>Daftar transaksi dan pembaruan status cucian Anda.</p></div><a class="text-link" href="{{ route('profile', ['tab' => 'orders']) }}">Lihat Semua Pesanan →</a></div>
            <div class="orders-wrap"><table class="orders-table"><thead><tr><th>NOMOR PESANAN</th><th>TANGGAL</th><th>LAYANAN</th><th>TOTAL HARGA</th><th>STATUS</th><th>AKSI</th></tr></thead><tbody>
                @forelse ($recentOrders as $order)
                    <tr><td class="order-number">▤ #WSH-{{ str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) }}</td><td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</td><td><span class="service-pill">{{ $order->service_name }}</span></td><td class="price-cell">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td><td><span class="table-status {{ $order->status }}">{{ ucfirst($order->status) }}</span></td><td><a class="detail-button" href="{{ route('orders.show', $order->id) }}">Lihat Detail ›</a></td></tr>
                @empty
                    <tr><td class="table-empty" colspan="6">Belum ada transaksi. Pesananmu akan muncul di sini setelah dibuat.</td></tr>
                @endforelse
            </tbody></table></div>
        </section>
        <aside class="tips-card">
            @if ($homePromo)
                <span class="tips-label">{{ $homePromo->promo_label ?: 'PROMO AKTIF' }}</span>
                <h2>{{ $homePromo->name }}</h2>
                <p>{{ $homePromo->store_name }} sedang menawarkan harga khusus untuk layanan ini.</p>
                <strong>Rp {{ number_format($homePromo->price, 0, ',', '.') }} <del style="color:#7d898f;font-size:13px;font-weight:500">Rp {{ number_format($homePromo->original_price, 0, ',', '.') }}</del></strong>
                <a class="text-link" href="{{ route('laundry.show', $homePromo->laundry_id) }}?service={{ $homePromo->service_id }}#booking-form">Lihat promo →</a>
            @else
                <span class="tips-label">TIPS LAUNDRY</span>
                <h2>Pisahkan warna sebelum mencuci</h2>
                <p>Kelompokkan pakaian putih dan berwarna agar warna tetap cerah dan tidak luntur.</p>
            @endif
        </aside>
        </div>
        <section class="support-banner"><div class="support-copy"><span class="support-icon" aria-hidden="true">♧</span><div><h2>Butuh bantuan dengan pesanan?</h2><p>Buka detail pesanan untuk menghubungi laundry yang menangani cucianmu.</p></div></div><a class="support-link" href="{{ route('profile', ['tab' => 'orders']) }}">Lihat Pesanan</a></section>
    </main>
    <footer class="dash-footer"><div class="dash-footer-inner"><a class="dash-brand" href="{{ route('dashboard') }}"><img class="dash-brand-mark" src="{{ asset('images/washly-logo.png') }}" alt="">Washly</a><span>© {{ now()->year }} Washly Laundry Care.</span><form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Keluar</button></form></div></footer>
    <script src="{{ asset('user/user.js') }}" defer></script>
    <script src="{{ asset('js/site-motion.js') }}" defer></script>
</body>

</html>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Facture KOBLAN' }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* =========================================================================
           STYLE LUXURY DARK THEME - FACTURE
           ========================================================================= */
        :root {
            --bg-dark: #0a0a0c;
            --bg-card: #121216;
            --gold-light: #FBEA9D;
            --gold-main: #D4AF37;
            --gold-dark: #997A15;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --border-glass: rgba(212, 175, 55, 0.15);
            --gradient-gold: linear-gradient(135deg, #FBEA9D 0%, #D4AF37 50%, #997A15 100%);
        }

        body { 
            font-family: 'Outfit', sans-serif; 
            background: var(--bg-dark); 
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(212, 175, 55, 0.05), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(212, 175, 55, 0.05), transparent 25%);
            margin: 0; 
            padding: 2rem 0; 
            color: var(--text-main);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .invoice-wrapper {
            max-width: 850px;
            margin: 0 auto;
            position: relative;
            padding: 0 1rem;
        }

        .invoice-container {
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 4rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.8), 0 0 40px rgba(212, 175, 55, 0.1);
        }

        /* Effet Holographique & Filigrane */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 15rem;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 900;
            color: rgba(255, 255, 255, 0.02);
            pointer-events: none;
            z-index: 1;
            white-space: nowrap;
        }

        .gold-border-top {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: var(--gradient-gold);
        }

        /* HEADER */
        .header { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 4rem; 
            position: relative;
            z-index: 2;
        }
        
        .logo { display: flex; align-items: center; gap: 1rem; }
        .logo-icon { 
            background: var(--gradient-gold); 
            color: #000; 
            width: 50px; height: 50px; 
            border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.8rem; font-weight: 900; 
            font-family: 'Space Grotesk', sans-serif;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
        }
        .logo-text { font-family: 'Space Grotesk', sans-serif; font-size: 1.8rem; font-weight: 800; letter-spacing: 2px; }
        .logo-sub { display: block; font-size: 0.8rem; color: var(--gold-main); font-family: 'Outfit', sans-serif; letter-spacing: 3px; text-transform: uppercase; margin-top: -5px; }

        .receipt-title { text-align: right; }
        .receipt-title h1 { 
            margin: 0; font-size: 3rem; 
            font-family: 'Space Grotesk', sans-serif; 
            background: var(--gradient-gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.1em; 
        }
        .receipt-title p { margin: 0.5rem 0 0; color: var(--text-muted); font-size: 1.1rem; }
        .receipt-title strong { color: var(--text-main); font-family: 'Space Grotesk', sans-serif; }
        
        .badge-statut { 
            display: inline-block; padding: 0.35rem 1rem; border-radius: 8px; 
            font-size: 0.85rem; font-weight: 700; text-transform: uppercase; 
            margin-top: 1rem; letter-spacing: 1px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .statut-terminee { border-color: #10b981; color: #10b981; box-shadow: 0 0 15px rgba(16,185,129,0.2); }
        .statut-en_cours, .statut-in_progress { border-color: var(--gold-main); color: var(--gold-main); box-shadow: 0 0 15px rgba(212,175,55,0.2); }
        .statut-annulee, .statut-cancelled { border-color: #ef4444; color: #ef4444; box-shadow: 0 0 15px rgba(239,68,68,0.2); }
        .statut-en_attente, .statut-pending, .statut-acceptee, .statut-confirmed, .statut-accepted { border-color: #3b82f6; color: #3b82f6; box-shadow: 0 0 15px rgba(59,130,246,0.2); }

        /* DETAILS (Client / Prestataire) */
        .details-grid { 
            display: grid; grid-template-columns: 1fr 1fr; gap: 2.5rem; 
            margin-bottom: 3.5rem; 
            position: relative; z-index: 2;
        }
        .info-box { 
            background: rgba(0,0,0,0.3); 
            padding: 2rem; border-radius: 16px; 
            border: 1px solid var(--border-glass); 
            position: relative;
        }
        .info-box::before {
            content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%;
            background: var(--gradient-gold); border-radius: 16px 0 0 16px;
        }
        .info-box h3 { margin: 0 0 1.2rem; font-size: 0.85rem; color: var(--gold-main); text-transform: uppercase; letter-spacing: 0.1em; }
        .info-box p { margin: 0 0 0.5rem; font-weight: 700; font-size: 1.3rem; font-family: 'Space Grotesk', sans-serif;}
        .info-box span { display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted); font-size: 0.95rem; margin-bottom: 0.4rem; }
        .info-box span i { color: var(--gold-dark); width: 15px; }

        /* TABLE */
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 3rem; position: relative; z-index: 2; }
        th { 
            text-align: left; padding: 1.2rem 1rem; 
            background: rgba(212, 175, 55, 0.05); 
            color: var(--gold-main); text-transform: uppercase; 
            font-size: 0.8rem; letter-spacing: 1px;
            border-top: 1px solid var(--border-glass);
            border-bottom: 1px solid var(--border-glass);
        }
        th:first-child { border-radius: 12px 0 0 12px; border-left: 1px solid var(--border-glass); }
        th:last-child { border-radius: 0 12px 12px 0; border-right: 1px solid var(--border-glass); }
        
        td { padding: 1.5rem 1rem; border-bottom: 1px dashed rgba(255,255,255,0.1); color: var(--text-main); font-size: 1.1rem; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; font-family: 'Space Grotesk', sans-serif; }

        /* TOTALS */
        .totals-wrapper { display: flex; justify-content: flex-end; position: relative; z-index: 2; }
        .totals { 
            width: 350px; 
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--border-glass); 
            border-radius: 16px; 
            overflow: hidden; 
        }
        .totals-row { display: flex; justify-content: space-between; padding: 1.2rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .totals-row span:first-child { color: var(--text-muted); }
        .totals-row.final { 
            background: var(--gradient-gold); 
            color: #000; font-weight: 900; font-size: 1.5rem; 
            font-family: 'Space Grotesk', sans-serif;
            border-bottom: none;
        }

        /* FOOTER */
        .footer { 
            margin-top: 5rem; text-align: center; color: var(--text-muted); 
            font-size: 0.9rem; padding-top: 2rem; 
            border-top: 1px solid rgba(255,255,255,0.1); 
            position: relative; z-index: 2;
        }
        
        .no-print { text-align: center; margin-bottom: 2rem; }
        .btn { 
            display: inline-flex; align-items: center; gap: 0.5rem; 
            background: var(--gradient-gold); color: #000; 
            padding: 0.8rem 2rem; text-decoration: none; border-radius: 12px; 
            font-weight: 800; cursor: pointer; border: none; font-family: 'Space Grotesk', sans-serif; 
            font-size: 1.1rem; transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(212,175,55,0.3); }
        
        .btn-outline {
            background: transparent; border: 1px solid var(--text-muted); color: var(--text-main);
        }
        .btn-outline:hover { background: rgba(255,255,255,0.05); box-shadow: none; }

        /* MEDIA PRINT DEFINITION */
        @media print {
            @page { size: A4 portrait; margin: 0; }
            body { 
                background: var(--bg-dark) !important; 
                padding: 0 !important; 
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
            }
            .no-print { display: none !important; }
            .invoice-wrapper { width: 100%; max-width: 100%; padding: 0; }
            .invoice-container { 
                box-shadow: none !important; 
                border: none !important; 
                border-radius: 0 !important; 
                padding: 3rem !important; 
                min-height: 100vh;
            }
            * { text-shadow: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn"><i class="fas fa-print"></i> IMPRIMER / PDF</button>
        <button onclick="window.close(); history.back();" class="btn btn-outline" style="margin-left:1rem;"><i class="fas fa-arrow-left"></i> RETOUR</button>
    </div>

    <div class="invoice-wrapper">
        <div class="invoice-container">
            <div class="gold-border-top"></div>
            
            <div class="watermark">KOBLAN</div>

            <!-- Header Section -->
            <div class="header">
                <div class="logo">
                    <div class="logo-icon">K</div>
                    <div>
                        <div class="logo-text">KOBLAN</div>
                        <span class="logo-sub">Services Premium CI</span>
                    </div>
                </div>
                
                <div class="receipt-title">
                    <h1>FACTURE</h1>
                    <p>Référence <strong>{{ $order->reference }}</strong></p>
                    <p>Émis le {{ $order->created_at->format('d/m/Y') }}</p>
                    <div class="badge-statut statut-{{ strtolower($order->status) }}">
                        <i class="fas fa-circle" style="font-size:0.5rem; vertical-align:middle; margin-right:4px;"></i> 
                        {{ $order->getStatusLabel() }}
                    </div>
                </div>
            </div>

            <!-- Details Section -->
            <div class="details-grid">
                <div class="info-box">
                    <h3>Facturé À</h3>
                    <p>{{ $order->client->name }}</p>
                    <span><i class="fas fa-phone-alt"></i> {{ $order->client->phone ?? 'Non renseigné' }}</span>
                    <span><i class="fas fa-envelope"></i> {{ $order->client->email }}</span>
                    <span style="margin-top:0.8rem;"><i class="fas fa-map-marker-alt"></i> {{ $order->address ?? 'Côte d\'Ivoire' }}</span>
                </div>
                
                <div class="info-box">
                    <h3>Prestataire Exécutant</h3>
                    <p>{{ $order->prestataire->name }}</p>
                    <span><i class="fas fa-phone-alt"></i> {{ $order->prestataire->phone ?? 'Non renseigné' }}</span>
                    <span><i class="fas fa-envelope"></i> {{ $order->prestataire->email }}</span>
                    <span style="margin-top:0.8rem;"><i class="fas fa-id-badge"></i> ID Pro : KOBLAN-{{ str_pad($order->prestataire->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            <!-- Order Items -->
            <table>
                <thead>
                    <tr>
                        <th>Désignation du Service</th>
                        <th class="text-right">Qté</th>
                        <th class="text-right">Prix Unitaire</th>
                        <th class="text-right">Total HT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong style="color:var(--text-main); font-family:'Space Grotesk', sans-serif; font-size:1.2rem;">
                                {{ $order->prestation->title ?? 'Service standard' }}
                            </strong>
                            <div style="font-size:0.9rem; color:var(--text-muted); margin-top:0.4rem;">
                                Réalisation de la prestation telle que convenue sur la plateforme sécurisée.
                            </div>
                        </td>
                        <td class="text-right">1</td>
                        <td class="text-right">{{ number_format($order->amount ?? 0, 0, ',', ' ') }} FCFA</td>
                        <td class="text-right font-bold" style="color:var(--gold-main);">{{ number_format($order->amount ?? 0, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="totals-wrapper">
                <div class="totals">
                    <div class="totals-row">
                        <span>Total (HT)</span>
                        <span style="color:var(--text-main);">{{ number_format($order->amount ?? 0, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="totals-row">
                        <span>Frais de service (TVA 0%)</span>
                        <span style="color:var(--text-main);">0 FCFA</span>
                    </div>
                    <div class="totals-row final">
                        <span>TOTAL TTC</span>
                        <span>{{ number_format($order->amount ?? 0, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>

            @if($order->status === 'completed')
            <div style="text-align:right; margin-top:1.5rem; color:#10b981; font-weight:700; font-size:1.1rem; display:flex; justify-content:flex-end; align-items:center; gap:0.5rem;">
                <i class="fas fa-check-circle" style="font-size:1.5rem;"></i> TRANSACTION FINALISÉE ET RÉGLÉE
            </div>
            @endif

            <!-- Footer -->
            <div class="footer">
                <p style="color:var(--gold-main); font-weight:600; letter-spacing:1px; margin-bottom:0.5rem;">Plateforme d'intermédiation KOBLAN SAS</p>
                <p>Abidjan, Côte d'Ivoire — Capital Social : 1.000.000 FCFA</p>
                <p style="font-size:0.8rem; margin-top:1rem; opacity:0.6;">Ceci est un reçu dématérialisé généré de manière automatique et certifiée. Pour toute réclamation, contactez support@koblan.ci</p>
            </div>
        </div>
    </div>
</body>
</html>

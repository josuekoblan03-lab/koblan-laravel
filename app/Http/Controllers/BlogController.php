<?php
namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /** Liste des articles */
    public function index()
    {
        $articles = Article::with('user')->publie()->orderByDesc('created_at')->get();
        return view('public.blog', compact('articles'));
    }

    /** Formulaire de création */
    public function create()
    {
        if (!Auth::check()) return redirect()->route('login');
        return view('public.blog_create');
    }

    /** Sauvegarde d'un article */
    public function store(Request $request)
    {
        if (!Auth::check()) return redirect()->route('login');

        $validated = $request->validate([
            'titre'     => 'required|string|max:255',
            'contenu'   => 'required|string|min:20',
            'categorie' => 'required|string|max:50',
            'media'     => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm|max:20480',
        ], [
            'titre.required'   => 'Le titre est obligatoire.',
            'contenu.required' => 'Le contenu est obligatoire.',
            'contenu.min'      => 'Le contenu doit faire au moins 20 caractères.',
        ]);

        $url_media  = null;
        $type_media = 'image';

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $path = $file->store('articles', 'public');
            $url_media = $path;
            $type_media = str_contains($file->getMimeType(), 'video') ? 'video' : 'image';
        }

        Article::create([
            'titre'      => $validated['titre'],
            'contenu'    => $validated['contenu'],
            'categorie'  => $validated['categorie'],
            'url_media'  => $url_media,
            'type_media' => $type_media,
            'user_id'    => Auth::id(),
            'statut'     => 'publie',
        ]);

        return redirect()->route('blog')->with('success', '🎉 Votre article a été publié avec succès !');
    }

    /** Détail d'un article */
    public function show($id)
    {
        $article = Article::with(['user', 'comments.user', 'likes'])->publie()->find($id);

        if ($article) {
            $article->increment('vues');
            $hasLiked = Auth::check() ? $article->likedByUser(Auth::id()) : false;
            $comments = $article->comments;
            $nb_likes = $article->likes()->count();

            $articleData = [
                'id'            => $article->id,
                'titre'         => $article->titre,
                'contenu'       => $article->contenu,
                'url_media'     => $article->url_media ? asset('storage/'.$article->url_media) : null,
                'type_media'    => $article->type_media,
                'date_creation' => $article->created_at->format('Y-m-d'),
                'vues'          => $article->vues,
                'nb_likes'      => $nb_likes,
                'categorie'     => $article->categorie,
                'auteur_prenom' => $article->user->name ?? 'Auteur',
                'auteur_nom'    => '',
                'is_real'       => true,
            ];

            return view('public.blog_detail', [
                'article'  => $articleData,
                'related'  => [],
                'comments' => $comments,
                'hasLiked' => $hasLiked,
            ]);
        }

        // Articles mock de démonstration
        $mocks = [
            1 => ['id'=>1,'titre'=>'Comment choisir le meilleur plombier pour vos réparations ?','contenu'=>"Découvrez nos conseils pratiques pour repérer un professionnel de confiance sur la plateforme KOBLAN.\n\nLa plomberie est un domaine délicat qui nécessite une expertise réelle. Un mauvais choix peut entraîner des dégâts considérables et des coûts de réparation élevés.\n\n## Vérifiez les certifications\n\nAvant de faire appel à un plombier, assurez-vous qu'il dispose des certifications nécessaires. En Côte d'Ivoire, un artisan sérieux peut présenter ses diplômes et justificatifs d'expérience.\n\n## Consultez les avis clients\n\nSur KOBLAN, chaque prestataire est noté par ses clients après chaque mission. Ces avis sont vérifiés et vous donnent une idée précise de la qualité du travail et du professionnalisme.\n\n## Comparez les devis\n\nN'acceptez jamais le premier prix sans avoir comparé. KOBLAN vous permet de contacter plusieurs prestataires et d'obtenir des devis gratuitement avant de vous engager.\n\n## Conclusion\n\nAvec ces conseils, vous êtes prêt à choisir le bon plombier pour vos besoins. N'hésitez pas à explorer notre catalogue de prestataires certifiés !",
                'url_media'=>'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?auto=format&fit=crop&w=1200&q=80','type_media'=>'image','date_creation'=>'2026-04-10','vues'=>1247,'nb_likes'=>58,'categorie'=>'Astuces','auteur_prenom'=>'Kouassi','auteur_nom'=>'Dje','is_real'=>false],
            2 => ['id'=>2,'titre'=>'Les nouvelles tendances de décoration intérieure en 2026','contenu'=>"Inspiration et idées créatives pour aménager votre intérieur avec des artisans locaux talentueux.\n\nLa décoration d'intérieur évolue constamment, et 2026 apporte son lot de nouvelles tendances passionnantes.\n\n## Le minimalisme tropical\n\nAllier la simplicité du minimalisme aux matériaux naturels de notre région : raphia, bois d'iroko, rotin...\n\n## Les couleurs terreuses\n\nOcre, terra cotta, beige sable... Les tons chauds inspirés de nos paysages ivoiriens sont en vogue.\n\n## L'artisanat local valorisé\n\nPoteries d'Abidjan, tissages de Korhogo, sculptures en bois de San-Pédro... Intégrer des œuvres d'artisans locaux est LA tendance déco de 2026.",
                'url_media'=>'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=80','type_media'=>'image','date_creation'=>'2026-04-08','vues'=>892,'nb_likes'=>34,'categorie'=>'Tendances','auteur_prenom'=>'Aminata','auteur_nom'=>'Coulibaly','is_real'=>false],
            3 => ['id'=>3,'titre'=>'Astuces pour un déménagement réussi et sans stress','contenu'=>"Organisez sereinement votre prochain déménagement avec l'aide de nos experts.\n\nUn déménagement réussi se prépare plusieurs semaines à l'avance.\n\n## 6 semaines avant\n\nFaites l'inventaire et contactez des déménageurs via KOBLAN pour des devis comparatifs.\n\n## 2 semaines avant\n\nEmballez les objets non essentiels et étiquetez chaque carton.\n\n## Le jour J\n\nSoyez présent pour superviser et vérifiez que rien n'est oublié.",
                'url_media'=>'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80','type_media'=>'image','date_creation'=>'2026-04-05','vues'=>654,'nb_likes'=>21,'categorie'=>'Conseils','auteur_prenom'=>'Ibrahim','auteur_nom'=>'Traoré','is_real'=>false],
            4 => ['id'=>4,'titre'=>'Pourquoi faire appel à un électricien certifié ?','contenu'=>"La sécurité électrique de votre logement ne doit jamais être négligée.\n\nChaque année, des milliers d'incendies domestiques sont causés par des installations électriques défectueuses.\n\n## Les risques d'une installation non conforme\n\nCourt-circuit, surcharge, risque d'électrocution... Les dangers sont nombreux et potentiellement mortels.\n\n## Comment reconnaître un bon électricien ?\n\nSur KOBLAN, tous nos électriciens sont vérifiés et leurs certifications validées par notre équipe.",
                'url_media'=>'https://images.unsplash.com/photo-1621905252507-b35492d90cb0?auto=format&fit=crop&w=1200&q=80','type_media'=>'image','date_creation'=>'2026-04-01','vues'=>430,'nb_likes'=>15,'categorie'=>'Sécurité','auteur_prenom'=>'Jean-Marc','auteur_nom'=>'Konan','is_real'=>false],
            5 => ['id'=>5,'titre'=>"Comment entretenir votre jardin tout au long de l'année ?",'contenu'=>"Un jardin bien entretenu valorise votre propriété.\n\nQuelques gestes simples suffisent pour le maintenir en parfait état toute l'année.\n\n## Les bases de l'entretien\n\nL'arrosage régulier, la taille saisonnière et la fertilisation sont les trois piliers d'un jardin sain.",
                'url_media'=>'https://images.unsplash.com/photo-1416879598555-22b311740d7c?auto=format&fit=crop&w=1200&q=80','type_media'=>'image','date_creation'=>'2026-03-28','vues'=>318,'nb_likes'=>12,'categorie'=>'Nature','auteur_prenom'=>'Fatou','auteur_nom'=>'Diallo','is_real'=>false],
            6 => ['id'=>6,'titre'=>'Les meilleures recettes de cuisine ivoirienne','contenu'=>"De l'attiéké au kedjenou, en passant par le garba, découvrez les plats emblématiques de la cuisine ivoirienne.\n\n## L'attiéké au poulet braisé\n\nPlat roi de la cuisine ivoirienne, l'attiéké accompagné de poulet braisé et de légumes frais est un incontournable.\n\n## Le kedjenou de poulet\n\nMijoté lentement dans une canari (poterie en terre cuite), ce plat concentre toutes les saveurs des épices locales.",
                'url_media'=>'https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=1200&q=80','type_media'=>'image','date_creation'=>'2026-03-22','vues'=>796,'nb_likes'=>42,'categorie'=>'Cuisine','auteur_prenom'=>'Marie-Louise','auteur_nom'=>'Bah','is_real'=>false],
        ];

        if (!isset($mocks[$id])) abort(404);

        return view('public.blog_detail', [
            'article'  => $mocks[$id],
            'related'  => collect($mocks)->filter(fn($m) => $m['id'] != $id && $m['categorie'] === $mocks[$id]['categorie'])->values()->take(3)->toArray(),
            'comments' => [],
            'hasLiked' => false,
        ]);
    }

    /** Like / Unlike un article (toggle, 1 seul like par user) */
    public function like($id)
    {
        if (!Auth::check()) {
            return response()->json(['action' => 'login_required'], 401);
        }

        $article = Article::find($id);
        if (!$article) {
            // Article mock, pas de persistance possible
            return response()->json(['action' => 'added', 'nb_likes' => 0]);
        }

        $existing = \App\Models\ArticleLike::where('article_id', $id)->where('user_id', Auth::id())->first();

        if ($existing) {
            // Déjà aimé → on unlike
            $existing->delete();
            $nb = $article->likes()->count();
            return response()->json(['action' => 'removed', 'nb_likes' => $nb]);
        } else {
            // Pas encore aimé → on like
            \App\Models\ArticleLike::create(['article_id' => $id, 'user_id' => Auth::id()]);
            $nb = $article->likes()->count();
            return response()->json(['action' => 'added', 'nb_likes' => $nb]);
        }
    }

    /** Poster un commentaire */
    public function comment(Request $request, $id)
    {
        $article = Article::find($id);
        if (!$article) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Impossible de commenter un article de démonstration.'], 400);
            }
            return back()->with('error', 'Impossible de commenter un article de démonstration.');
        }

        $request->validate([
            'contenu' => 'required|string|min:3|max:1000',
        ], [
            'contenu.required' => 'Le commentaire ne peut pas être vide.',
            'contenu.min'      => 'Le commentaire doit faire au moins 3 caractères.',
        ]);

        $comment = \App\Models\ArticleComment::create([
            'article_id' => $article->id,
            'user_id'    => Auth::id(),
            'contenu'    => $request->contenu,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'contenu' => $comment->contenu]);
        }

        return back()->with('success', '✅ Commentaire publié !');
    }

}

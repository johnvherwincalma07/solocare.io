<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\About;
use App\Models\Article;
use App\Models\Gallery;
use App\Models\Faq;

class HomeController extends Controller
{
    public function index()
    {
        $aboutContent = About::first()->content ?? '';
        $articles = Article::all();
        $gallery = Gallery::all();

        // Hardcoded requirements for left and right columns
        $requirementsLeft = [
            ['title' => '1. As a Consequence of Rape', 'content' => '• Birth Certificate/s of the child<br>• Complaint Affidavit<br>• Sworn affidavit declaring not cohabiting<br>• Medical Record<br>• Barangay Affidavit<br>• 2 pcs 1x1 ID picture'],
            ['title' => '2. Death of the Spouse', 'content' => '• Death Certificate<br>• Barangay certification stating solo parenting responsibility'],
            ['title' => '3. Detained Spouse', 'content' => '• Certificate of Detention<br>• Court or Police record<br>• Barangay certification of solo parenting'],
            ['title' => '4. Physical or Mental Incapacity of Spouse', 'content' => '• Medical Certificate from government hospital<br>• Barangay certification<br>• Valid ID'],
            ['title' => '5. Legal Separation / Annulment', 'content' => '• Court Decision<br>• Barangay certification stating custody of children<br>• 2 pcs ID picture'],
            ['title' => '6. Abandonment by Spouse', 'content' => '• Barangay or Police blotter<br>• Sworn affidavit<br>• Certification of non-cohabitation'],
        ];

        $requirementsRight = [
            ['title' => '7. Unmarried Mother/Father', 'content' => '• Birth Certificate of child<br>• CENOMAR<br>• Affidavit of Solo Parenting'],
            ['title' => '8. Legal Guardian', 'content' => '• Court Appointment as Guardian<br>• Barangay certification of custody'],
            ['title' => '9. Foster or Adoptive Parent', 'content' => '• DSWD Certification<br>• Adoption papers<br>• Barangay certification'],
            ['title' => '10. Spouse Working Abroad (6+ Months)', 'content' => '• POEA/Company Certification<br>• Passport/Travel records<br>• Barangay certification of solo responsibility'],
            ['title' => '11. Abandoned by Partner (Unmarried)', 'content' => '• Barangay blotter<br>• Sworn affidavit<br>• Certification of non-cohabitation for at least 1 year'],
            ['title' => '12. Other Circumstances (Court Declaration)', 'content' => '• Court order or certification<br>• Barangay certification<br>• Valid ID & affidavit'],
        ];

        // ✅ Fetch active FAQs from database
        $faqs = Faq::where('status', 'active')->orderBy('id', 'asc')->get();

        return view('home', compact(
            'aboutContent',
            'articles',
            'gallery',
            'requirementsLeft',
            'requirementsRight',
            'faqs' // ✅ Pass to view
        ));
    }

    public function getPublicFaqs()
    {
        return response()->json(
            Faq::where('is_active', 1)
                ->orderBy('id', 'asc')
                ->get()
        );
    }

}

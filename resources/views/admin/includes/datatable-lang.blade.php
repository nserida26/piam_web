@php $locale = app()->getLocale(); @endphp
@if($locale === 'ar')
var dtLang = {
    "sEmptyTable":     "لا توجد بيانات متاحة في الجدول",
    "sInfo":           "عرض _START_ إلى _END_ من أصل _TOTAL_ سجل",
    "sInfoEmpty":      "عرض 0 إلى 0 من أصل 0 سجل",
    "sInfoFiltered":   "(مرشح من إجمالي _MAX_ سجل)",
    "sLengthMenu":     "أظهر _MENU_ سجلاً",
    "sLoadingRecords": "جارٍ التحميل...",
    "sProcessing":     "جارٍ المعالجة...",
    "sSearch":         "بحث:",
    "sZeroRecords":    "لم يتم العثور على نتائج",
    "oPaginate": {
        "sFirst":    "الأول",
        "sLast":     "الأخير",
        "sNext":     "التالي",
        "sPrevious": "السابق"
    },
    "oAria": {
        "sSortAscending":  ": تفعيل لترتيب العمود تصاعدياً",
        "sSortDescending": ": تفعيل لترتيب العمود تنازلياً"
    }
};
@else
var dtLang = {
    "sEmptyTable":     "Aucune donnée disponible dans le tableau",
    "sInfo":           "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
    "sInfoEmpty":      "Affichage de l'élément 0 à 0 sur 0 élément",
    "sInfoFiltered":   "(filtré à partir de _MAX_ éléments au total)",
    "sLengthMenu":     "Afficher _MENU_ éléments",
    "sLoadingRecords": "Chargement...",
    "sProcessing":     "Traitement...",
    "sSearch":         "Rechercher :",
    "sZeroRecords":    "Aucun résultat trouvé",
    "oPaginate": {
        "sFirst":    "Premier",
        "sLast":     "Dernier",
        "sNext":     "Suivant",
        "sPrevious": "Précédent"
    },
    "oAria": {
        "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
        "sSortDescending": ": activer pour trier la colonne par ordre décroissant"
    }
};
@endif

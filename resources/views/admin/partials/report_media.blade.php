{{--
    Photos + positions GPS d'un rapport dans une fenêtre de détail.
    Usage JS : renderMedia($('#conteneur'), photos, gps)
      photos = [{url, label}], gps = [{label, lat, lng, url}]  (App\Services\ReportMedia)
--}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<style>
    .media-photos { display: flex; flex-wrap: wrap; gap: 8px; }
    .media-photos figure { margin: 0; width: 140px; }
    .media-photos img { width: 140px; height: 105px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; cursor: zoom-in; }
    .media-photos figcaption { font-size: 11px; color: #666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .media-map { height: 260px; border-radius: 4px; border: 1px solid #ddd; }
</style>
<div class="modal fade" id="photo-zoom" tabindex="-1" style="z-index:1060">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0 py-1">
                <span class="text-white small" id="photo-zoom-label"></span>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center p-1"><img id="photo-zoom-img" src="" class="img-fluid" alt=""></div>
        </div>
    </div>
</div>
<script>
window.renderMedia = function ($el, photos, gps) {
    var esc = function (v) { return $('<div>').text(v == null ? '' : v).html(); };
    var html = '';
    html += '<h6 class="mb-2"><i class="fas fa-camera"></i> Photos (' + photos.length + ')</h6>';
    if (photos.length) {
        html += '<div class="media-photos mb-3">';
        $.each(photos, function (i, p) {
            html += '<figure><img src="' + esc(p.url) + '" data-label="' + esc(p.label) + '" class="media-photo" loading="lazy" alt="">'
                + '<figcaption title="' + esc(p.label) + '">' + esc(p.label) + '</figcaption></figure>';
        });
        html += '</div>';
    } else {
        html += '<p class="text-muted small mb-3">Aucune photo envoyée.</p>';
    }
    html += '<h6 class="mb-2"><i class="fas fa-map-marker-alt"></i> Positions GPS (' + gps.length + ')</h6>';
    if (gps.length) {
        var mapId = 'media-map-' + Date.now();
        html += '<div id="' + mapId + '" class="media-map mb-2"></div><ul class="small mb-0 pl-3">';
        $.each(gps, function (i, g) {
            html += '<li>' + esc(g.label) + ' : ' + g.lat.toFixed(6) + ', ' + g.lng.toFixed(6)
                + ' — <a href="' + esc(g.url) + '" target="_blank" rel="noopener">Google Maps</a></li>';
        });
        html += '</ul>';
        $el.html(html);
        // La carte est dessinée quand la fenêtre est visible (taille correcte).
        setTimeout(function () {
            if (!window.L) return;
            var map = L.map(mapId);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);
            var pts = $.map(gps, function (g) {
                L.marker([g.lat, g.lng]).addTo(map).bindPopup(esc(g.label));
                return [[g.lat, g.lng]];
            });
            if (pts.length === 1) map.setView(pts[0], 16); else map.fitBounds(pts, { padding: [20, 20], maxZoom: 17 });
        }, 400);
    } else {
        html += '<p class="text-muted small mb-0">Aucune position GPS envoyée.</p>';
        $el.html(html);
    }
};
$(document).on('click', '.media-photo', function () {
    $('#photo-zoom-img').attr('src', $(this).attr('src'));
    $('#photo-zoom-label').text($(this).data('label'));
    $('#photo-zoom').modal('show');
});
</script>

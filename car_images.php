<?php
// Real car images - high quality sources
function get_car_image($car_name) {
     $images = [
        'Toyota Camry' => 'https://toyotacanada.scene7.com/is/image/toyotacanada/C_005?ts=1681945044398&$Media-Large$&dpr=off',
        'Honda Civic' => 'https://cdn.motor1.com/images/mgl/1ZzGoW/s3/honda-civic-sedan-e-hev-2023.webp',
        'Ford Mustang' => 'https://images.topgear.com.ph/topgear/images/2022/09/15/all-new-ford-mustang-2023-unveiled-20-1663207575.webp',
        'Tesla Model 3' => 'https://images.unsplash.com/photo-1617704548623-340376564e68?auto=format&fit=crop&w=1200&q=80',
        'Jeep Wrangler' => 'https://images.cars.com/cldstatic/wp-content/uploads/jeep-wrangler-willys-4xe-2023-exterior-oem-02.jpg',
        'BMW X5' => 'https://images.unsplash.com/photo-1731142582229-e0ee70302c02?auto=format&fit=crop&w=1200&q=80',
        'Mercedes-Benz C-Class' => 'https://www.iihs.org/cdn-cgi/image/width=636/api/ratings/model-year-images/3367/',
        'Chevrolet Suburban' => 'https://chevrolet.com.ph/wp-content/uploads/2022/06/suburban-gallery-1.jpg',
        'Nissan Altima' => 'https://di-uploads-pod2.dealerinspire.com/bobmoorenissan/uploads/2022/11/2023-nissan-altima-113-1654783718.jpeg',
        'Hyundai Tucson' => 'https://www.iihs.org/cdn-cgi/image/width=636/api/ratings/model-year-images/3254/',
        'Porsche 911' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1200&q=80',
        'Range Rover' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=80',
        'Ferrari 488' => 'https://4kwallpapers.com/images/wallpapers/ferrari-488-pista-1920x1080-10172.jpeg',
        'Lamborghini Huracan' => 'https://upload.wikimedia.org/wikipedia/commons/c/ca/Lamborghini_Huracan_EVO_coup%C3%A8_in_Giallo_Inti.jpg',
        'Audi R8' => 'https://carwithprice.com/wp-content/uploads/2024/10/Audi-R8.jpg',
        'McLaren 720S' => 'https://cdn.motor1.com/images/mgl/xe22G/s1/mclaren-720s-track-pack.jpg',
        'Bentley Continental' => 'https://www.hrowen.co.uk/wp-content/uploads/2025/04/1-Continental-GT-Large.jpg',
        'Rolls-Royce Ghost' => 'https://media.ed.edmunds-media.com/rolls-royce/ghost/2025/ot/2025_rolls-royce_ghost_f34_ot_31925_1280.jpg',
        'Maserati Levante' => 'https://www.topgear.com/sites/default/files/images/cars-road-test/carousel/2019/07/dba162697542e15e32529ffddd7d3233/maserati_levante_trofeo_dynamic_18.jpg?w=892&h=502',
        'Lexus LC 500' => 'https://www.longolexus.com/blogs/3079/wp-content/uploads/2023/08/2022_lexus_lc-500-convertible_convertible_base_fq_oem_1_1600.jpg',
        'Cadillac Escalade' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=1200&q=80'
    ];
    return $images[$car_name] ?? 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=400&h=250&fit=crop&auto=format';
}
?>


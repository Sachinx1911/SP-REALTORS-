# SP Realtors Child Theme

Parent theme (`sp-realtors`) update केला तरी तुमचे customization सुरक्षित राहावेत यासाठी हा child theme आहे. Design/content data (properties, locations, testimonials, enquiries) plugin मध्ये असल्याने theme बदल पूर्णपणे presentation-पुरता मर्यादित आहे.

## कसे वापरायचे
1. wp-admin → Appearance → Themes → **SP Realtors Child** activate करा (Template header मुळे parent theme `sp-realtors` install/active असणे आवश्यक आहे).
2. `style.css` च्या शेवटी तुमचे custom CSS लिहा — हे parent च्या `assets/css/main.css` नंतर load होते, त्यामुळे तेच selectors वापरून override करता येतात.

## Template override
कोणताही parent template file (उदा. `template-parts/card-property.php`, `single-property.php`) child theme मध्ये त्याच नावाने/path ने copy करा — WordPress आपोआप child theme ची copy वापरेल, parent ची नाही.

## CSS variables override
Parent theme चे color tokens `:root` वर CSS custom properties म्हणून आहेत (`--spr-navy`, `--spr-blue`, `--spr-green`, `--spr-gold`, इ. — बरेचसे Customizer मधून auto-generate होतात). Fixed override हवा असल्यास `style.css` मध्ये:

```css
:root {
	--spr-navy: #0b2f57;
}
```

## हे का वापरायचे
- Parent theme update (bug fix / नवीन feature) आल्यावर तुमचे custom CSS/templates गायब होणार नाहीत.
- Broker चा data (properties/enquiries/testimonials) कायम प्लगिन मध्येच राहतो — थीम किंवा child theme बदलला तरी परिणाम होत नाही.

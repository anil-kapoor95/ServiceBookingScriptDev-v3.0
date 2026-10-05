<?php
/**
 * Theme 11 - picks a universal line icon for a service card from the service
 * name (falling back to its description), so each card shows an icon that
 * relates to that service. Rules are tried top to bottom and match at the
 * START of a word, case-insensitive (so "car" matches "Car wash" but not
 * "carpet"). Services that match nothing get the neutral concierge-bell icon.
 * To add or re-order keywords, edit $pjSbsServiceIconRules below.
 * Icons: Lucide (ISC licence), drawn with currentColor.
 */
if (!function_exists('pjSbsServiceIcon'))
{
	function pjSbsServiceIcon($title, $description = '')
	{
		static $icons = null, $rules = null;
		if ($icons === null)
		{
			$icons = array(
				'bed-double' => '<path d="M2 20v-8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8"/> <path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/> <path d="M12 4v6"/> <path d="M2 18h20"/>',
				'bike' => '<circle cx="18.5" cy="17.5" r="3.5"/> <circle cx="5.5" cy="17.5" r="3.5"/> <circle cx="15" cy="5" r="1"/> <path d="M12 17.5V14l-3-3 4-3 2 3h2"/>',
				'bug' => '<path d="M12 20v-9"/> <path d="M14 7a4 4 0 0 1 4 4v3a6 6 0 0 1-12 0v-3a4 4 0 0 1 4-4z"/> <path d="M14.12 3.88 16 2"/> <path d="M21 21a4 4 0 0 0-3.81-4"/> <path d="M21 5a4 4 0 0 1-3.55 3.97"/> <path d="M22 13h-4"/> <path d="M3 21a4 4 0 0 1 3.81-4"/> <path d="M3 5a4 4 0 0 0 3.55 3.97"/> <path d="M6 13H2"/> <path d="m8 2 1.88 1.88"/> <path d="M9 7.13V6a3 3 0 1 1 6 0v1.13"/>',
				'calculator' => '<rect width="16" height="20" x="4" y="2" rx="2"/> <line x1="8" x2="16" y1="6" y2="6"/> <line x1="16" x2="16" y1="14" y2="18"/> <path d="M16 10h.01"/> <path d="M12 10h.01"/> <path d="M8 10h.01"/> <path d="M12 14h.01"/> <path d="M8 14h.01"/> <path d="M12 18h.01"/> <path d="M8 18h.01"/>',
				'camera' => '<path d="M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.759-1.048l.489-.904A2 2 0 0 1 10.004 4z"/> <circle cx="12" cy="13" r="3"/>',
				'car' => '<path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/> <circle cx="7" cy="17" r="2"/> <path d="M9 17h6"/> <circle cx="17" cy="17" r="2"/>',
				'concierge-bell' => '<path d="M3 20a1 1 0 0 1-1-1v-1a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v1a1 1 0 0 1-1 1Z"/> <path d="M20 16a8 8 0 1 0-16 0"/> <path d="M12 4v4"/> <path d="M10 4h4"/>',
				'droplets' => '<path d="M7 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S7.29 6.75 7 5.3c-.29 1.45-1.14 2.84-2.29 3.76S3 11.1 3 12.25c0 2.22 1.8 4.05 4 4.05z"/> <path d="M12.56 6.6A10.97 10.97 0 0 0 14 3.02c.5 2.5 2 4.9 4 6.5s3 3.5 3 5.5a6.98 6.98 0 0 1-11.91 4.97"/>',
				'dumbbell' => '<path d="M17.596 12.768a2 2 0 1 0 2.829-2.829l-1.768-1.767a2 2 0 0 0 2.828-2.829l-2.828-2.828a2 2 0 0 0-2.829 2.828l-1.767-1.768a2 2 0 1 0-2.829 2.829z"/> <path d="m2.5 21.5 1.4-1.4"/> <path d="m20.1 3.9 1.4-1.4"/> <path d="M5.343 21.485a2 2 0 1 0 2.829-2.828l1.767 1.768a2 2 0 1 0 2.829-2.829l-6.364-6.364a2 2 0 1 0-2.829 2.829l1.768 1.767a2 2 0 0 0-2.828 2.829z"/> <path d="m9.6 14.4 4.8-4.8"/>',
				'flower-2' => '<path d="M12 5a3 3 0 1 1 3 3m-3-3a3 3 0 1 0-3 3m3-3v1M9 8a3 3 0 1 0 3 3M9 8h1m5 0a3 3 0 1 1-3 3m3-3h-1m-2 3v-1"/> <circle cx="12" cy="8" r="2"/> <path d="M12 10v12"/> <path d="M12 22c4.2 0 7-1.667 7-5-4.2 0-7 1.667-7 5Z"/> <path d="M12 22c-4.2 0-7-1.667-7-5 4.2 0 7 1.667 7 5Z"/>',
				'graduation-cap' => '<path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"/> <path d="M22 10v6"/> <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"/>',
				'hand' => '<path d="M18 11V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2"/> <path d="M14 10V4a2 2 0 0 0-2-2a2 2 0 0 0-2 2v2"/> <path d="M10 10.5V6a2 2 0 0 0-2-2a2 2 0 0 0-2 2v8"/> <path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/>',
				'heart-pulse' => '<path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/> <path d="M3.22 13H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"/>',
				'house' => '<path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/> <path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
				'laptop' => '<path d="M18 5a2 2 0 0 1 2 2v8.526a2 2 0 0 0 .212.897l1.068 2.127a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45l1.068-2.127A2 2 0 0 0 4 15.526V7a2 2 0 0 1 2-2z"/> <path d="M20.054 15.987H3.946"/>',
				'paintbrush' => '<path d="m14.622 17.897-10.68-2.913"/> <path d="M18.376 2.622a1 1 0 1 1 3.002 3.002L17.36 9.643a.5.5 0 0 0 0 .707l.944.944a2.41 2.41 0 0 1 0 3.408l-.944.944a.5.5 0 0 1-.707 0L8.354 7.348a.5.5 0 0 1 0-.707l.944-.944a2.41 2.41 0 0 1 3.408 0l.944.944a.5.5 0 0 0 .707 0z"/> <path d="M9 8c-1.804 2.71-3.97 3.46-6.583 3.948a.507.507 0 0 0-.302.819l7.32 8.883a1 1 0 0 0 1.185.204C12.735 20.405 16 16.792 16 15"/>',
				'palette' => '<path d="M12 22a1 1 0 0 1 0-20 10 9 0 0 1 10 9 5 5 0 0 1-5 5h-2.25a1.75 1.75 0 0 0-1.4 2.8l.3.4a1.75 1.75 0 0 1-1.4 2.8z"/> <circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/> <circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/> <circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/> <circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/>',
				'party-popper' => '<path d="M5.8 11.3 2 22l10.7-3.79"/> <path d="M4 3h.01"/> <path d="M22 8h.01"/> <path d="M15 2h.01"/> <path d="M22 20h.01"/> <path d="m22 2-2.24.75a2.9 2.9 0 0 0-1.96 3.12c.1.86-.57 1.63-1.45 1.63h-.38c-.86 0-1.6.6-1.76 1.44L14 10"/> <path d="m22 13-.82-.33c-.86-.34-1.82.2-1.98 1.11c-.11.7-.72 1.22-1.43 1.22H17"/> <path d="m11 2 .33.82c.34.86-.2 1.82-1.11 1.98C9.52 4.9 9 5.52 9 6.23V7"/> <path d="M11 13c1.93 1.93 2.83 4.17 2 5-.83.83-3.07-.07-5-2-1.93-1.93-2.83-4.17-2-5 .83-.83 3.07.07 5 2Z"/>',
				'paw-print' => '<circle cx="11" cy="4" r="2"/> <circle cx="18" cy="8" r="2"/> <circle cx="20" cy="16" r="2"/> <path d="M9 10a5 5 0 0 1 5 5v3.5a3.5 3.5 0 0 1-6.84 1.045Q6.52 17.48 4.46 16.84A3.5 3.5 0 0 1 5.5 10Z"/>',
				'pen-line' => '<path d="M13 21h8"/> <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>',
				'plane' => '<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>',
				'scale' => '<path d="M12 3v18"/> <path d="m19 8 3 8a5 5 0 0 1-6 0zV7"/> <path d="M3 7h1a17 17 0 0 0 8-2 17 17 0 0 0 8 2h1"/> <path d="m5 8 3 8a5 5 0 0 1-6 0zV7"/> <path d="M7 21h10"/>',
				'scissors' => '<circle cx="6" cy="6" r="3"/> <path d="M8.12 8.12 12 12"/> <path d="M20 4 8.12 15.88"/> <circle cx="6" cy="18" r="3"/> <path d="M14.8 14.8 20 20"/>',
				'shield-check' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/> <path d="m9 12 2 2 4-4"/>',
				'smile' => '<path d="M15 10V9"/> <path d="M16.472 15a6 6 0 01-8.943 0"/> <path d="M9 10V9"/> <circle cx="12" cy="12" r="10"/>',
				'snowflake' => '<path d="m10 20-1.25-2.5L6 18"/> <path d="M10 4 8.75 6.5 6 6"/> <path d="m14 20 1.25-2.5L18 18"/> <path d="m14 4 1.25 2.5L18 6"/> <path d="m17 21-3-6h-4"/> <path d="m17 3-3 6 1.5 3"/> <path d="M2 12h6.5L10 9"/> <path d="m20 10-1.5 2 1.5 2"/> <path d="M22 12h-6.5L14 15"/> <path d="m4 10 1.5 2L4 14"/> <path d="m7 21 3-6-1.5-3"/> <path d="m7 3 3 6h4"/>',
				'sparkles' => '<path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"/> <path d="M20 2v4"/> <path d="M22 4h-4"/> <circle cx="4" cy="20" r="2"/>',
				'spray-can' => '<path d="M3 3h.01"/> <path d="M7 5h.01"/> <path d="M11 7h.01"/> <path d="M3 7h.01"/> <path d="M7 9h.01"/> <path d="M3 11h.01"/> <rect width="4" height="4" x="15" y="5"/> <path d="m19 9 2 2v10c0 .6-.4 1-1 1h-6c-.6 0-1-.4-1-1V11l2-2"/> <path d="m13 14 8-2"/> <path d="m13 19 8-2"/>',
				'sprout' => '<path d="M14 9.536V7a4 4 0 0 1 4-4h1.5a.5.5 0 0 1 .5.5V5a4 4 0 0 1-4 4 4 4 0 0 0-4 4c0 2 1 3 1 5a5 5 0 0 1-1 3"/> <path d="M4 9a5 5 0 0 1 8 4 5 5 0 0 1-8-4"/> <path d="M5 21h14"/>',
				'stethoscope' => '<path d="M11 2v2"/> <path d="M5 2v2"/> <path d="M5 3H4a2 2 0 0 0-2 2v4a6 6 0 0 0 12 0V5a2 2 0 0 0-2-2h-1"/> <path d="M8 15a6 6 0 0 0 12 0v-3"/> <circle cx="20" cy="10" r="2"/>',
				'truck' => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/> <path d="M15 18H9"/> <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/> <circle cx="17" cy="18" r="2"/> <circle cx="7" cy="18" r="2"/>',
				'utensils' => '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/> <path d="M7 2v20"/> <path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>',
				'wand-sparkles' => '<path d="m21.64 3.64-1.28-1.28a1.21 1.21 0 0 0-1.72 0L2.36 18.64a1.21 1.21 0 0 0 0 1.72l1.28 1.28a1.2 1.2 0 0 0 1.72 0L21.64 5.36a1.2 1.2 0 0 0 0-1.72"/> <path d="m14 7 3 3"/> <path d="M5 6v4"/> <path d="M19 14v4"/> <path d="M10 2v2"/> <path d="M7 8H3"/> <path d="M21 16h-4"/> <path d="M11 3H9"/>',
				'wrench' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"/>',
				'zap' => '<path d="M15.914 4a1.5 1.5 0 00-2.474-1.561l-9 9A1.5 1.5 0 005.5 14h4.002a.5.5 0 01.471.666L8.086 20a1.5 1.5 0 002.475 1.56l9-9A1.5 1.5 0 0018.5 10h-3.997a.5.5 0 01-.472-.667z"/>',
			);
			$rules = array(
				array('/\b(?:paint|spray|body ?work)|\bdents?\b/i', 'paintbrush'),
				array('/\b(?:wash)s?\b|\b(?:rinse|laundry|dry ?clean)/i', 'droplets'),
				array('/\b(?:ppf|tint)s?\b|\b(?:coating|ceramic|protect|armou?r)/i', 'shield-check'),
				array('/\b(?:wax|buff)s?\b|\b(?:detail|polish|shine)/i', 'sparkles'),
				array('/\b(?:cut|trim)s?\b|\b(?:hair|barber|shave|beard|salon|style|styling|blow ?dry|color|colour|cutting)/i', 'scissors'),
				array('/\b(?:nail)s?\b|\b(?:manicure|pedicure)/i', 'hand'),
				array('/\b(?:spa)s?\b|\b(?:massage|relax|aromatherap)/i', 'flower-2'),
				array('/\b(?:skin|lash|brow)s?\b|\b(?:facial|makeup|make-up|beauty|threading)/i', 'wand-sparkles'),
				array('/\b(?:physio|therap|rehab|wellness|chiropract|acupunct)/i', 'heart-pulse'),
				array('/\b(?:tooth|teeth|dent(?:al|ist)|orthodont)/i', 'smile'),
				array('/\b(?:doctor|medical|clinic|consult|check-?up|nurse|health|vaccin)/i', 'stethoscope'),
				array('/\b(?:vet|pet|dog|cat)s?\b|\b(?:groom)/i', 'paw-print'),
				array('/\b(?:gym|yoga)s?\b|\b(?:fitness|train|pilates|workout|crossfit|boxing)/i', 'dumbbell'),
				array('/\b(?:exam)s?\b|\b(?:tutor|lesson|class|course|teach|coach|school|learn)/i', 'graduation-cap'),
				array('/\b(?:film)s?\b|\b(?:photo|video|shoot|portrait)/i', 'camera'),
				array('/\b(?:law)s?\b|\b(?:legal|attorney|notary)/i', 'scale'),
				array('/\b(?:tax)s?\b|\b(?:account|financ|bookkeep|audit|payroll)/i', 'calculator'),
				array('/\b(?:pc|web|tech|data)s?\b|\b(?:laptop|computer|phone|mobile|software|it support|network)/i', 'laptop'),
				array('/\b(?:maid|mop)s?\b|\b(?:clean|housekeep|janitor|sanitiz|disinfect|sweep|vacuum)/i', 'spray-can'),
				array('/\b(?:electric|wiring|lighting|solar)/i', 'zap'),
				array('/\b(?:ac |a\/c|hvac)s?\b|\b(?:air ?con|heating|cooling|refrigerat)/i', 'snowflake'),
				array('/\b(?:lawn|tree|mow)s?\b|\b(?:garden|landscap|plant)/i', 'sprout'),
				array('/\b(?:pest|bug)s?\b|\b(?:termite|rodent)/i', 'bug'),
				array('/\b(?:move|haul)s?\b|\b(?:moving|removal|deliver|courier|transport|shipping|freight|truck)/i', 'truck'),
				array('/\b(?:tour|trip|visa)s?\b|\b(?:flight|travel|ticket|airport)/i', 'plane'),
				array('/\b(?:stay|room|bnb)s?\b|\b(?:hotel|accommodat|lodging)/i', 'bed-double'),
				array('/\b(?:event|wedding|party|celebrat|birthday)/i', 'party-popper'),
				array('/\b(?:food|cook|chef|meal|cake)s?\b|\b(?:cater|dinner|lunch|restaurant)/i', 'utensils'),
				array('/\b(?:art|draw|logo)s?\b|\b(?:design|paint(?:ing)?|brand)/i', 'palette'),
				array('/\b(?:ink)s?\b|\b(?:tattoo|piercing)/i', 'pen-line'),
				array('/\b(?:pipe|leak|fix|oil|tyre|tire)s?\b|\b(?:plumb|drain|repair|mechanic|maintenance|engine|brake|wheel|alignment|install|replace|garage)/i', 'wrench'),
				array('/\bbikes?\b|\b(?:bicycle|cycl|motorcycle|scooter)/i', 'bike'),
				array('/\b(?:car|auto|taxi|cab|van|suv)s?\b|\b(?:vehicle|drive|driving)/i', 'car'),
				array('/\b(?:home|roof)s?\b|\b(?:house|propert|real ?estate|inspection|carpent|handyman|furnitur)/i', 'house'),
			);
		}
		foreach (array((string) $title, (string) $description) as $text)
		{
			if ($text === '') { continue; }
			foreach ($rules as $rule)
			{
				if (preg_match($rule[0], $text)) { return $icons[$rule[1]]; }
			}
		}
		return $icons['concierge-bell'];
	}
}

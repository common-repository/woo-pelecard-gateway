<?php

namespace Pelecardwc;
use Pelecardwc\Traits\Singleton;
class Transaction extends \WC_Data {

	const META_KEY = '_wppc_transaction';
	const ACTION_TYPE_J2 = '2';
	const ACTION_TYPE_J4 = '4';
	const ACTION_TYPE_J5 = '5';

	protected $id = '';

	protected $data = [
		'status_code' => '',
		'error_message' => '',
	];

	protected $validate = true;

	public function __construct( string $transaction_id = '' ) {
		parent::__construct();

		$this->set_id( $transaction_id );

		if ( ! empty( $this->get_id() ) ) {
			$this->set_data( $this->get_transaction() );
		}
	}

	public function set_order_id( int $order_id ) {
		$this->add_meta_data( 'ParamX', $order_id, true );

		return $this;
	}

	public function get_id(): string {
		return $this->id;
	}

	public function set_id( $transaction_id ) {
		$this->id = $transaction_id;

		return $this;
	}

	public function set_data( array $data ) {

		if ( isset( $data['is_get_transaction_response'] ) && $data['is_get_transaction_response'] ) {
	        $this->update_meta_data('is_get_transaction_response', '1', true);

	    }

		$ResultData =  $data['ResultData'];


		if ( ! empty( $ResultData ) ) {
			$ShvaResultEmv = $ResultData['ShvaResultEmv'];
			$ShvaResultEmvMessage = $ResultData['ShvaResultEmvMessage'];
		}

		if ( !empty($ShvaResultEmv) ){

			if ( empty($ShvaResultEmvMessage) ){

				switch ($ShvaResultEmv) {

					case '000': $ShvaResultEmvMessage = 'מאושר'; break;
					case '001': $ShvaResultEmvMessage = 'כרטיס חסום'; break;
					case '002': $ShvaResultEmvMessage = 'גנוב החרם כרטיס'; break;
					case '003': $ShvaResultEmvMessage = 'התקשר לחברת האשראי'; break;
					case '004': $ShvaResultEmvMessage = 'העסקה לא אושרה'; break;
					case '005': $ShvaResultEmvMessage = 'כרטיס מזוייף החרם'; break;
					case '006': $ShvaResultEmvMessage = 'דחה עסקה: cvv2 שגוי'; break;
					case '007': $ShvaResultEmvMessage = 'דחה עסקה: cavv/ucaf שגוי'; break;
					case '008': $ShvaResultEmvMessage = 'דחה עסקה: avs שגוי'; break;
					case '009': $ShvaResultEmvMessage = 'דחייה - נתק בתקשורת'; break;
					case '010': $ShvaResultEmvMessage = 'אישור חלקי'; break;
					case '011': $ShvaResultEmvMessage = 'דחה עסקה: חוסר בנקודות/כוכבים/מיילים/הטבה אחרת'; break;
					case '012': $ShvaResultEmvMessage = 'בכרטיס לא מורשה במסוף'; break;
					case '013': $ShvaResultEmvMessage = 'דחה בקשה .קוד יתרה שגוי'; break;
					case '014': $ShvaResultEmvMessage = 'דחייה .כרטיס לא משוייך לרשת'; break;
					case '015': $ShvaResultEmvMessage = 'דחה עסקה: הכרטיס אינו בתוקף'; break;
					case '016': $ShvaResultEmvMessage = 'דחייה -אין הרשאה לסוג מטבע'; break;
					case '017': $ShvaResultEmvMessage = 'דחייה -אין הרשאה לסוג אשראי בעסקה'; break;
					case '026': $ShvaResultEmvMessage = 'דחה עסקהID או CVV שגוי'; break;
					case '041': $ShvaResultEmvMessage = 'ישנה חובת יציאה לשאילתא בגין תקרה בלבד לעסקה עם פרמטר j2'; break;
					case '042': $ShvaResultEmvMessage = 'ישנה חובת יציאה לשאילתא לא רק בגין תקרה, לעסקה עם פרמטר j2'; break;
					case '051': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 1'; break;
					case '052': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 4'; break;
					case '053': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 6'; break;
					case '055': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 11'; break;
					case '056': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 12'; break;
					case '057': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 15'; break;
					case '058': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 18'; break;
					case '059': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 31'; break;
					case '060': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 34'; break;
					case '061': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 41'; break;
					case '062': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 44'; break;
					case '063': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 64'; break;
					case '064': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 80'; break;
					case '065': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 81'; break;
					case '066': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 82'; break;
					case '067': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 83'; break;
					case '068': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 90'; break;
					case '069': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 91'; break;
					case '070': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 92'; break;
					case '071': $ShvaResultEmvMessage = 'חסר קובץ ווקטור 93'; break;
					case '073': $ShvaResultEmvMessage = 'חסר קובץ PARAM_3_1'; break;
					case '074': $ShvaResultEmvMessage = 'חסר קובץ PARAM_3_2'; break;
					case '075': $ShvaResultEmvMessage = 'חסר קובץ PARAM_3_3'; break;
					case '076': $ShvaResultEmvMessage = 'חסר קובץ PARAM_3_4'; break;
					case '077': $ShvaResultEmvMessage = 'חסר קובץ PARAM_361'; break;
					case '078': $ShvaResultEmvMessage = 'חסר קובץ PARAM_363'; break;
					case '079': $ShvaResultEmvMessage = 'חסר קובץ PARAM_364'; break;
					case '080': $ShvaResultEmvMessage = 'חסר קובץ PARAM_61'; break;
					case '081': $ShvaResultEmvMessage = 'חסר קובץ PARAM_62'; break;
					case '082': $ShvaResultEmvMessage = 'חסר קובץ PARAM_63'; break;
					case '083': $ShvaResultEmvMessage = 'חסר קובץ CEIL_41'; break;
					case '084': $ShvaResultEmvMessage = 'חסר קובץ CEIL_42'; break;
					case '085': $ShvaResultEmvMessage = 'חסר קובץ CEIL_43'; break;
					case '086': $ShvaResultEmvMessage = 'חסר קובץ CEIL_44'; break;
					case '087': $ShvaResultEmvMessage = 'חסר קובץ DATA'; break;
					case '088': $ShvaResultEmvMessage = 'חסר קובץ JENR'; break;
					case '089': $ShvaResultEmvMessage = 'חסר קובץ Start'; break;
					case '101': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 1'; break;
					case '103': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 4'; break;
					case '104': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 6'; break;
					case '106': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 11'; break;
					case '107': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 12'; break;
					case '108': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 15'; break;
					case '110': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 18'; break;
					case '111': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 31'; break;
					case '112': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 34'; break;
					case '113': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 41'; break;
					case '114': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 44'; break;
					case '116': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 64'; break;
					case '117': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 81'; break;
					case '118': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 82'; break;
					case '119': $ShvaResultEmvMessage = 'חסרה כניסה בוקטור 83'; break;
					case '120': $ShvaResultEmvMessage = 'כרטיס לא קיים, שגיאה בהקלדת כרטיס'; break;
					case '121': $ShvaResultEmvMessage = 'סולק לא קיים, נא לבצע שידור לצורך עדכון פרמטרים ווקטורים'; break;
					case '122': $ShvaResultEmvMessage = 'מותג לא קיים, נא לבצע שידור לצורך עדכון פרמטרים ווקטורים'; break;
					case '123': $ShvaResultEmvMessage = 'מנפיק לא קיים, נא לבצע שידור לצורך עדכון פרמטרים ווקטורים'; break;
					case '141': $ShvaResultEmvMessage = 'אין אישור לעבודה עם המותג הזה, כרטיס ישראלי'; break;
					case '142': $ShvaResultEmvMessage = 'אין אישור לעבודה עם המותג הזה, כרטיס תייר'; break;
					case '143': $ShvaResultEmvMessage = 'אין אישור לעבודה עם כרטיס המועדון ,כרטיס של ישראכרט'; break;
					case '144': $ShvaResultEmvMessage = '-אין אישור לעבודה עם כרטיס המועדון ,כרטיס של MAX'; break;
					case '145': $ShvaResultEmvMessage = 'אין אישור לעבודה עם כרטיס המועדון ,כרטיס של כאל'; break;
					case '146': $ShvaResultEmvMessage = 'אין אישור לעבודה עם המותג פרטי'; break;
					case '147': $ShvaResultEmvMessage = 'חסרה כניסה בקובץ תקרות לכרטיסים ישראלים שאינם PLשיטה 4.2 0'; break;
					case '148': $ShvaResultEmvMessage = 'חסרה כניסה בקובץ תקרות לכרטיסים ישראלים שאינם PLשיטה 4.3 1'; break;
					case '149': $ShvaResultEmvMessage = 'חסרה כניסה בקובץ תקרות לכרטיסי תייר 4.4'; break;
					case '150': $ShvaResultEmvMessage = 'חסרה כניסה בקובץ כרטיסים תקפים -ישראכרט'; break;
					case '151': $ShvaResultEmvMessage = 'חסרה כניסה בקובץ כרטיסים תקפים -כאל'; break;
					case '152': $ShvaResultEmvMessage = 'חסרה כניסה בקובץ כרטיסים תקפים -מנפיק עתידי'; break;
					case '182': $ShvaResultEmvMessage = 'שגיאה בערכי וקטור 4'; break;
					case '183': $ShvaResultEmvMessage = 'שגיאה בערכי וקטור 6/12'; break;
					case '186': $ShvaResultEmvMessage = 'שגיאה בערכי וקטור 18'; break;
					case '187': $ShvaResultEmvMessage = 'שגיאה בערכי וקטור 34'; break;
					case '188': $ShvaResultEmvMessage = 'שגיאה בערכי וקטור 64'; break;
					case '190': $ShvaResultEmvMessage = 'שגיאה בערכי וקטור 90'; break;
					case '191': $ShvaResultEmvMessage = 'נתונים לא תקינים בוקטור הרשאות מנפיק'; break;
					case '192': $ShvaResultEmvMessage = 'נתונים לא ולידים בסט הפרמטרים'; break;
					case '193': $ShvaResultEmvMessage = 'נתונים לא ולידים בקובץ פרמטרים ברמת מסוף'; break;
					case '300': $ShvaResultEmvMessage = 'אין הרשאה לסוג עסקה - הרשאת סולק'; break;
					case '301': $ShvaResultEmvMessage = 'אין הרשאה למטבע - הרשאת סולק'; break;
					case '303': $ShvaResultEmvMessage = 'אין הרשאת סולק לביצוע עסקה כאשר הכרטיס לא נוכח'; break;
					case '304': $ShvaResultEmvMessage = 'אין הרשאה לאשראי - הרשאת סולק'; break;
					case '308': $ShvaResultEmvMessage = 'אין הרשאה להצמדה - הרשאת סולק'; break;
					case '309': $ShvaResultEmvMessage = 'אין הרשאת סולק לאשראי במועד קבוע'; break;
					case '310': $ShvaResultEmvMessage = 'אין הרשאה להקלדת מספר אישור מראש'; break;
					case '311': $ShvaResultEmvMessage = 'אין הרשאה לבצע עסקאות לקוד שרות 587'; break;
					case '312': $ShvaResultEmvMessage = 'אין הרשאת סולק לאשראי דחוי'; break;
					case '313': $ShvaResultEmvMessage = 'אין הרשאת סולק להטבות'; break;
					case '314': $ShvaResultEmvMessage = 'אין הרשאת סולק למבצעים'; break;
					case '315': $ShvaResultEmvMessage = 'אין הרשאת סולק לקוד מבצע ספציפי'; break;
					case '316': $ShvaResultEmvMessage = 'אין הרשאת סולק לעסקת טעינה'; break;
					case '317': $ShvaResultEmvMessage = 'אין הרשאת סולק לטעינה/פריקה בקוד אמצעי התשלום בשילוב קוד מטבע'; break;
					case '318': $ShvaResultEmvMessage = 'אין הרשאת סולק למטבע בסוג אשראי זה'; break;
					case '319': $ShvaResultEmvMessage = 'אין הרשאת סולק לטיפ'; break;
					case '321': $ShvaResultEmvMessage = 'אין לכרטיס נטען הרשאה לביצוע בקשה לאישור'; break;
					case '322': $ShvaResultEmvMessage = 'אין הרשאה מתאימה לביצוע בקשה לאישור ללא עסקה'; break;
					case '323': $ShvaResultEmvMessage = 'אין הרשאה לביצוע אישור בקשה יזומה ע"י קמעונאי'; break;
					case '341': $ShvaResultEmvMessage = 'אין הרשאה לעסקה - הרשאת מנפיק'; break;
					case '342': $ShvaResultEmvMessage = 'אין הרשאה למטבע - הרשאת מנפיק'; break;
					case '343': $ShvaResultEmvMessage = 'אין הרשאת מנפיק לביצוע עסקה כאשר הכרטיס לא נוכח'; break;
					case '344': $ShvaResultEmvMessage = 'אין הרשאה לאשראי - הרשאת מנפיק'; break;
					case '348': $ShvaResultEmvMessage = 'אין הרשאה לביצוע אישור בקשה יזומה ע"י קמעונאי'; break;
					case '349': $ShvaResultEmvMessage = 'אין הרשאה מתאימה לביצוע בקשה לאישור ללא עסקה J5'; break;
					case '350': $ShvaResultEmvMessage = 'אין הרשאת מנפיק להטבות'; break;
					case '351': $ShvaResultEmvMessage = 'אין הרשאת מנפיק לאשראי דחוי'; break;
					case '352': $ShvaResultEmvMessage = 'אין הרשאת מנפיק לעסקת טעינה'; break;
					case '353': $ShvaResultEmvMessage = 'אין הרשאת מנפיק לטעינה/פריקה בקוד אמצעי התשלום'; break;
					case '354': $ShvaResultEmvMessage = 'אין הרשאת מנפיק למטבע בסוג אשראי זה'; break;
					case '381': $ShvaResultEmvMessage = 'אין הרשאה לבצע עסקת contactlessמעל סכום מרבי'; break;
					case '382': $ShvaResultEmvMessage = 'במסוף המוגדר כשרות עצמי ניתן לבצע רק עסקאות בשירות עצמי'; break;
					case '384': $ShvaResultEmvMessage = 'מסוף מוגדר כרב-ספק /מוטב - חסר מספר ספק/מוטב'; break;
					case '385': $ShvaResultEmvMessage = 'במסוף המוגדר כמסוף סחר אלקטרוני חובה להעביר eci'; break;
					case '401': $ShvaResultEmvMessage = 'מספר התשלומים גדול מערך שדה מספר תשלומים מקסימלי'; break;
					case '402': $ShvaResultEmvMessage = 'מספר התשלומים קטן מערך שדה מספר תשלומים מינימלי'; break;
					case '403': $ShvaResultEmvMessage = 'סכום העסקה קטן מערך שדה סכום מינמלי לתשלום !!!'; break;
					case '404': $ShvaResultEmvMessage = 'לא הוזן שדה מספר תשלומים'; break;
					case '405': $ShvaResultEmvMessage = 'חסר נתון סכום תשלום ראשון /קבוע'; break;
					case '406': $ShvaResultEmvMessage = 'סה"כ סכום העסקה שונה מסכום תשלום ראשון +סכום תשלום קבוע *מספר תשלומים'; break;
					case '408': $ShvaResultEmvMessage = 'ערוץ 2 קצר מ-37 תווים'; break;
					case '410': $ShvaResultEmvMessage = 'דחיה מסיבת dcode'; break;
					case '414': $ShvaResultEmvMessage = 'בעסקה עם חיוב בתאריך קבוע הוכנס תאריך מאוחר משנה מבצוע העיסקה'; break;
					case '415': $ShvaResultEmvMessage = 'הוזנו נתונים לא תקינים'; break;
					case '416': $ShvaResultEmvMessage = 'תאריך תוקף לא במבנה תקין'; break;
					case '417': $ShvaResultEmvMessage = 'מספר מסוף אינו תקין'; break;
					case '418': $ShvaResultEmvMessage = 'חסרים פרמטרים חיוניים (להודעת שגיאה זו מתווספת רשימת הפרמטרים החסרים)'; break;
					case '419': $ShvaResultEmvMessage = 'שגיאה בהעברת מאפיין clientInputPan'; break;
					case '420': $ShvaResultEmvMessage = 'מספר כרטיס לא ולידי -במצב של הזנת ערוץ 2בעסקה ללא כרטיס נוכח'; break;
					case '421': $ShvaResultEmvMessage = 'שגיאה כללי -נתונים לא ולידים'; break;
					case '422': $ShvaResultEmvMessage = 'שגיאה בבנית מסר ISO'; break;
					case '424': $ShvaResultEmvMessage = 'שדה לא נומרי'; break;
					case '425': $ShvaResultEmvMessage = 'רשומה כפולה'; break;
					case '426': $ShvaResultEmvMessage = 'הסכום הוגדל לאחר ביצוע בדיקות אשראית'; break;
					case '428': $ShvaResultEmvMessage = 'חסר קוד שרות בכרטיס'; break;
					case '429': $ShvaResultEmvMessage = 'כרטיס אינו תקף לפי קובץ כרטיסים תקפים'; break;
					case '431': $ShvaResultEmvMessage = 'שגיאה כללית'; break;
					case '432': $ShvaResultEmvMessage = 'אין הראשה להעברת כרטיס דרך קורא מגנטי'; break;
					case '433': $ShvaResultEmvMessage = 'חיוב להעביר ב - PinPad'; break;
					case '434': $ShvaResultEmvMessage = 'אסור להעביר כרטיס במכשיר ה- PinPad'; break;
					case '435': $ShvaResultEmvMessage = 'המכשיר לא מוגדר להעברת כרטיס מגנטי CTL'; break;
					case '436': $ShvaResultEmvMessage = 'המכשיר לא מוגדר להעברת כרטיס EMV CTL'; break;
					case '439': $ShvaResultEmvMessage = 'אין הרשאה לסוג אשראי לפי סוג עסקה'; break;
					case '440': $ShvaResultEmvMessage = 'כרטיס תייר אינו מורשה לסוג אשראי זה'; break;
					case '441': $ShvaResultEmvMessage = 'אין הרשאה לביצוע סוג עסקה - כרטיס קיים בוקטור 80'; break;
					case '442': $ShvaResultEmvMessage = 'אין לבצע Stand-inלאימות אישור לסולק זה'; break;
					case '443': $ShvaResultEmvMessage = 'לא ניתן לבצע עסקת ביטול - כרטיס לא נמצא בקובץ תנועות הקיים במסוף'; break;
					case '445': $ShvaResultEmvMessage = 'בכרטיס חיוב מיידי ניתן לבצע אשראי חיוב מיידי בלבד'; break;
					case '447': $ShvaResultEmvMessage = 'מספר כרטיס שגוי'; break;
					case '448': $ShvaResultEmvMessage = 'חיוב להקליד כתובת לקוח (מיקוד ,מספר בית ועיר)'; break;
					case '449': $ShvaResultEmvMessage = 'חיוב להקליד מיקוד'; break;
					case '450': $ShvaResultEmvMessage = 'קוד מבצע מחוץ לתחום, צ"ל בתחום 1-12'; break;
					case '451': $ShvaResultEmvMessage = 'שגיאה במהלך בנית רשומת עסקה'; break;
					case '452': $ShvaResultEmvMessage = 'בעסקת טעינה/פריקה/בירור יתרה חיוב להזין שדה קוד אמצעי תשלום'; break;
					case '453': $ShvaResultEmvMessage = 'אין אפשרות לבטל עסקת פריקה 7.9.3'; break;
					case '455': $ShvaResultEmvMessage = 'לא ניתן לבצע עסקת חיוב מאולצת כאשר נדרשת בקשה לאישור (למעט תקרות)'; break;
					case '456': $ShvaResultEmvMessage = "כרטיס נמצא בקובץ תנועות עם קוד תשובה 'החרם כרטיס'"; break;
					case '457': $ShvaResultEmvMessage = 'בכרטיס חיוב מיידי מותרת עסקת חיוב רגילה/זיכוי/ביטול'; break;
					case '458': $ShvaResultEmvMessage = 'קוד מועדון לא בתחום'; break;
					case '470': $ShvaResultEmvMessage = 'בעסקת הו"ק סכום התשלומים גבוה משדה סכום העסקה'; break;
					case '471': $ShvaResultEmvMessage = 'בעסקת הו"ק מספר תשלום תורן גדול מסה"כ מספר התשלומים'; break;
					case '472': $ShvaResultEmvMessage = 'בעסקת חיוב עם מזומן חיוב להזין סכום במזומן'; break;
					case '473': $ShvaResultEmvMessage = 'בעסקת חיוב עם מזומן סכום המזומן צריך להיות קטן מסכום העסקה'; break;
					case '474': $ShvaResultEmvMessage = 'עסקת איתחול בהוראת קבע מחייבת פרמטר J5'; break;
					case '475': $ShvaResultEmvMessage = 'עסקת ה"ק מחייבת אחד מהשדות: מספר תשלומים או סכום כולל'; break;
					case '476': $ShvaResultEmvMessage = 'עסקת תורן בהוראת קבע מחייבת שדה מספר תשלום'; break;
					case '477': $ShvaResultEmvMessage = 'עסקת תורן בהוראת קבע מחייבת מספר מזהה של עסקת איתחול'; break;
					case '478': $ShvaResultEmvMessage = 'עסקת תורן בהוראת קבע מחייבת מספר אישור של עסקת איתחול'; break;
					case '479': $ShvaResultEmvMessage = 'עסקת תורן בהוראת קבע מחייבת שדות תאריך וזמן עסקת איתחול'; break;
					case '480': $ShvaResultEmvMessage = 'חסר שדה מאשר עסקת מקור'; break;
					case '481': $ShvaResultEmvMessage = 'חסר שדה מספר יחידות כאשר העסקה מתבצעת בקוד אמצעי תשלום השונה ממטבע'; break;
					case '482': $ShvaResultEmvMessage = 'בכרטיס נטען מותרת עסקת חיוב רגילה/זיכוי/ביטול/פריקה/טעינה/בירור יתרה'; break;
					case '483': $ShvaResultEmvMessage = 'עסקה עם כרטיס דלק במסוף דלק חיוב להזין מספר רכב'; break;
					case '484': $ShvaResultEmvMessage = 'מספר רכב המוקלד שונה ממספר הרכב הצרוב ע"ג הפס המגנטי/מספר בנק שונה מ-012/ספרות שמאליות של מספר הסניף שונה מ-44'; break;
					case '485': $ShvaResultEmvMessage = 'מספר רכב קצר מ- 6ספרות /שונה ממספר הרכב המופיע ע"ג ערוץ 2 (פוזיציה 34 בערוץ 2) כרטיס מאפיין דלק של לאומי קארד'; break;
					case '486': $ShvaResultEmvMessage = 'ישנה חובת הקלדת קריאת מונה (פוזיציה 30בערוץ )2כרטיס מאפיין דלק של לאומי קארד'; break;
					case '487': $ShvaResultEmvMessage = 'רק במסוף המוגדר כדלק דו שלבי ניתן להשתמש בעדכון אובליגו'; break;
					case '489': $ShvaResultEmvMessage = 'בכרטיס דלקן מותרת עסקת חיוב רגילה בלבד (עסקת ביטול אסורה)'; break;
					case '490': $ShvaResultEmvMessage = 'בכרטיסי דלק/דלקן/דלק מועדון ניתן לבצע עסקאות רק במסופי דלק'; break;
					case '491': $ShvaResultEmvMessage = 'עסקה הכוללת המרה חייבת להכיל את כל השדות conversion_rate_06, conversion_rate_09, conversion_currency_51'; break;
					case '492': $ShvaResultEmvMessage = 'אין המרה על עסקאות שקל/דולר'; break;
					case '493': $ShvaResultEmvMessage = 'בעסקה הכוללת הטבה חיוב שיהיו רק אחד מהשדות הבאים: סכום הנחה/מספר יחידות/% ההנחה'; break;
					case '494': $ShvaResultEmvMessage = 'מספר מסוף שונה'; break;
					case '495': $ShvaResultEmvMessage = 'אין הרשאת fallback'; break;
					case '496': $ShvaResultEmvMessage = 'לא ניתן להצמיד אשראי השונה מאשראי קרדיט/תשלומים'; break;
					case '497': $ShvaResultEmvMessage = 'לא ניתן להצמיד לדולר/מדד במטבע השונה משקל'; break;
					case '498': $ShvaResultEmvMessage = 'כרטיס ישראכרט מקומי הספרטור צ"ל בפוזיציה 18'; break;
					case '500': $ShvaResultEmvMessage = 'העסקה הופסקה ע"י המשתמש'; break;
					case '504': $ShvaResultEmvMessage = 'חוסר התאמה בין שדה מקור נתוני הכרטיס לשדה מספר כרטיס'; break;
					case '505': $ShvaResultEmvMessage = 'ערך לא חוקי בשדה סוג עסקה'; break;
					case '506': $ShvaResultEmvMessage = 'ערך לא חוקי בשדה eci'; break;
					case '507': $ShvaResultEmvMessage = 'סכום העסקה בפועל גבוה מהסכום המאושר'; break;
					case '509': $ShvaResultEmvMessage = 'שגיאה במהלך כתיבה לקובץ תנועות'; break;
					case '512': $ShvaResultEmvMessage = 'לא ניתן להכניס אישור שהתקבל ממענה קולי לעסקה זו'; break;
					case '551': $ShvaResultEmvMessage = 'מסר תשובה אינו מתאים למסר הבקשה'; break;
					case '552': $ShvaResultEmvMessage = 'שגיאה בשדה 55'; break;
					case '553': $ShvaResultEmvMessage = 'התקבלה שגיאה מהטנדם'; break;
					case '554': $ShvaResultEmvMessage = 'במסר התשובה חסר שדה mcc_18'; break;
					case '555': $ShvaResultEmvMessage = 'במסר התשובה חסר שדה response_code_25'; break;
					case '556': $ShvaResultEmvMessage = 'במסר התשובה חסר שדה rrn_37'; break;
					case '557': $ShvaResultEmvMessage = 'במסר התשובה חסר שדה comp_retailer_num_42'; break;
					case '558': $ShvaResultEmvMessage = 'במסר התשובה חסר שדה auth_code_43'; break;
					case '559': $ShvaResultEmvMessage = 'במסר התשובה חסר שדה f39_response_39'; break;
					case '560': $ShvaResultEmvMessage = 'במסר התשובה חסר שדה authorization_no_38'; break;
					case '561': $ShvaResultEmvMessage = 'במסר התשובה חסר/ריק שדה additional_data_48.solek_auth_no'; break;
					case '562': $ShvaResultEmvMessage = 'במסר התשובה חסר אחד מהשדות conversion_amount_06, conversion_rate_09, conversion_currency_51'; break;
					case '563': $ShvaResultEmvMessage = 'ערך השדה אינו מתאים למספרי האישור שהתקבלו auth_code_43'; break;
					case '564': $ShvaResultEmvMessage = 'במסר התשובה חסר/ריק שדה additional_amunts54.cashback_amount'; break;
					case '565': $ShvaResultEmvMessage = 'אי-התאמה בין שדה 25לשדה 43'; break;
					case '566': $ShvaResultEmvMessage = 'במסוף המוגדר כתומך בדלק דו-שלבי יש חובה להחזיר שדות 90,119'; break;
					case '567': $ShvaResultEmvMessage = 'שדות 25,127לא תקינים במסר עידכון אובליגו במסוף המוגדר כדלק דו-שלבי'; break;
					case '598': $ShvaResultEmvMessage = 'ERROR_IN_NEG_FILE'; break;
					case '599': $ShvaResultEmvMessage = 'שגיאה כללית'; break;
					case '700': $ShvaResultEmvMessage = 'עסקה נדחתה ע"י מכשיר PinPad'; break;
					case '701': $ShvaResultEmvMessage = 'שגיאה במכשיר pinpad'; break;
					case '702': $ShvaResultEmvMessage = 'יציאת com לא תקינה'; break;
					case '703': $ShvaResultEmvMessage = 'PINPAD_TransactionError'; break;
					case '704': $ShvaResultEmvMessage = 'PINPAD_TransactionCancelled'; break;
					case '705': $ShvaResultEmvMessage = 'PINPAD_UserCancelled'; break;
					case '706': $ShvaResultEmvMessage = 'PINPAD_UserTimeout'; break;
					case '707': $ShvaResultEmvMessage = 'PINPAD_UserCardRemoved'; break;
					case '708': $ShvaResultEmvMessage = 'PINPAD_UserRetriesExceeded'; break;
					case '709': $ShvaResultEmvMessage = 'PINPAD_PINPadTimeout'; break;
					case '710': $ShvaResultEmvMessage = 'PINPAD_PINPadCommsError'; break;
					case '711': $ShvaResultEmvMessage = 'PINPAD_PINPadMessageError'; break;
					case '712': $ShvaResultEmvMessage = 'PINPAD_PINPadNotInitialized'; break;
					case '713': $ShvaResultEmvMessage = 'PINPAD_PINPadCardReadError'; break;
					case '714': $ShvaResultEmvMessage = 'PINPAD_ReaderTimeout'; break;
					case '715': $ShvaResultEmvMessage = 'PINPAD_ReaderCommsError'; break;
					case '716': $ShvaResultEmvMessage = 'PINPAD_ReaderMessageError'; break;
					case '717': $ShvaResultEmvMessage = 'PINPAD_HostMessageError'; break;
					case '718': $ShvaResultEmvMessage = 'PINPAD_HostConfigError'; break;
					case '719': $ShvaResultEmvMessage = 'PINPAD_HostKeyError'; break;
					case '720': $ShvaResultEmvMessage = 'PINPAD_HostConnectError'; break;
					case '721': $ShvaResultEmvMessage = 'PINPAD_HostTransmitError'; break;
					case '722': $ShvaResultEmvMessage = 'PINPAD_HostReceiveError'; break;
					case '723': $ShvaResultEmvMessage = 'PINPAD_HostTimeout'; break;
					case '724': $ShvaResultEmvMessage = 'PINVerificationNotSupportedByCard'; break;
					case '725': $ShvaResultEmvMessage = 'PINVerificationFailed'; break;
					case '726': $ShvaResultEmvMessage = 'שגיאה בקליטת קובץ config.xml'; break;
					case '730': $ShvaResultEmvMessage = 'מכשיר אישר עסקה בניגוד להחלטת אשראית'; break;
					case '731': $ShvaResultEmvMessage = 'כרטיס לא הוכנס'; break;
					case '777': $ShvaResultEmvMessage = 'תקין, ניתן להמשיך'; break;
					case '998': $ShvaResultEmvMessage = 'שגיאה בקובץ חסומים'; break;
					default: $ShvaResultEmvMessage = 'תקלה כללית בדוק מול מחלקת התמיכה';

				}

				
			}
			/* set SHVA result excet some values */
			$good_shav_results = array(41, 42);

			if ( in_array($ShvaResultEmv, $good_shav_results) ){
				$this->set_props( [
					'status_code' => $data['StatusCode'] ?? '',
					'error_message' => $data['ErrorMessage'] ?? '',
				] );	
			} else{
				$this->set_props( [
					'status_code' => $ShvaResultEmv ?? '',
					'error_message' => $ShvaResultEmvMessage ?? '',
				] );
			}

		} else{
			$this->set_props( [
				'status_code' => $data['StatusCode'] ?? '',
				'error_message' => $data['ErrorMessage'] ?? '',
			] );	
		}

		if ( ! empty( $data['ResultData'] ) ) {
			foreach ( $data['ResultData'] as $key => $value ) {
				$this->add_meta_data( $key, $value, true );
			}
		}

		if ( ! empty( $data['UserData'] ) ) {
			foreach ( $data['UserData'] as $key => $value ) {
				$this->add_meta_data( $key, $value, true );
			}
		}

		if ( ! empty( $data['EZCountData'] ) ) {
			foreach ( $data['EZCountData'] as $key => $value ) {
				$this->add_meta_data( $key, $value, true );
			}
		}

		if ( ! empty( $data['ICountData'] ) ) {
			foreach ( $data['ICountData'] as $key => $value ) {
				$this->add_meta_data( $key, $value, true );
			}
		}

		if ( ! empty( $data['TamalData'] ) ) {
			foreach ( $data['TamalData'] as $key => $value ) {
				$this->add_meta_data( $key, $value, true );
			}
		}

		if ( empty( $this->get_id() ) ) {
			$this->set_id( $this->get_meta( 'PelecardTransactionId' ) );
		}

		$this->set_object_read( true );

		return $this;
	}

	public function get_transaction(): array {
		$result = $this->is_uuid() ? Api::get_transaction_by_id( $this->get_id() ) : [];

		return is_wp_error( $result ) ? [] : $result;
	}

	public function is_uuid(): bool {
		$pattern = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

		return (bool) preg_match( $pattern, $this->get_id() );
	}

	public function get_order_id( $context = 'view' ) {
		if ( $this->meta_exists( 'AdditionalDetailsParamX' ) ) {
			$order_id = $this->get_meta( 'AdditionalDetailsParamX', true, $context );
		} elseif ( $this->meta_exists( 'ParamX' ) ) {
			$order_id = $this->get_meta( 'ParamX', true, $context );
		} else {
			$order_id = 0;
		}

		return (int) apply_filters( 'wppc/transaction/order_id', $order_id, $context, $this );
	}

	public function set_json_data( $data ) {
		if ( is_string( $data ) ) {
			$data = json_decode( $data, true );
		}

		$this->set_props( $data );

		if ( ! empty( $data['meta_data'] ) ) {
			$this->set_meta_data( $data['meta_data'] );
		}

		return $this;
	}

	public function set_meta_data( $data ) {
		if ( empty( $data ) || ! is_array( $data ) ) {
			return;
		}

		$this->maybe_read_meta_data();

		foreach ( $data as $meta ) {
			$meta = (array) $meta;
			if ( isset( $meta['key'], $meta['value'] ) ) {
				$this->meta_data[] = new \WC_Meta_Data( [
					'key' => $meta['key'],
					'value' => $meta['value'],
				] );
			}
		}
	}

	public function get_meta_data_by_key($key) {
	    return get_post_meta($this->get_id(), $key, true);
	}

	public function set_validate( bool $validate ) {
		$this->validate = $validate;

		return $this;
	}

	public function needs_validation(): bool {
		return apply_filters( 'wppc/transaction/needs_validation', $this->validate, $this );
	}

	public function set_status_code( string $status_code ) {
		$this->set_prop( 'status_code', $status_code );

		return $this;
	}

	public function set_error_message( string $error_message ) {
		$this->set_prop( 'error_message', $error_message );

		return $this;
	}

	public function get_error_message( $context = 'view' ) {
		return $this->get_prop( 'error_message', $context );
	}

	public function validate(): bool {
		$confirmation_key = $this->get_meta( 'ConfirmationKey' );
		$debit_total = $this->get_meta( 'DebitTotal' );
		$unique_key = $this->get_unique_key();

		return Api::validate_by_unique_key( $confirmation_key, $debit_total, $unique_key );
	}

	public function get_unique_key() {
		$order = $this->get_order();
		if ( $order ) {
			return $order->get_order_key();
		}

		return Gateway::instance()->get_user_nonce( $this->get_user_id() );
	}

	public function get_order() {
		$order_id = $this->get_order_id();

		return wc_get_order( $order_id );
	}

	public function save() {
		$order = $this->get_order();
		if ( ! $order || $this->exists( $order ) ) {
			return;
		}

		$order->add_meta_data( self::META_KEY, (string) $this );
		$order->save();

		do_action( 'wppc/transaction/after_save', $this, $order );
	}

	public function is_token_valid(): bool {
		return 0 < $this->get_user_id() && ! empty( $this->get_token() );
	}

	public function get_user_id( $context = 'view' ): int {
		return (int) $this->get_meta( 'UserData1', true, $context );
	}

	public function get_timeout_redirect_url( $context = 'view' ): string {
		return $this->get_meta( 'UserData2', true, $context );
	}

	public function exists( \WC_Order $order ) {
		$transactions = Order::instance()->get_transactions( $order );

		return array_reduce( $transactions, function( $carry, $transaction ) {
			return $carry || $transaction->get_id() === $this->get_id();
		}, false );
	}

	public function flatten(): array {
		$flatten = [
			'StatusCode' => $this->get_status_code(),
			'ErrorMessage' => $this->get_error_message(),
		];

		foreach ( $this->get_meta_data() as $meta ) {
			$flatten[ $meta->get_data()['key'] ] = $meta->get_data()['value'];
		}

		return $flatten;
	}

	public function get_token( $context = 'view' ): string {
		return $this->get_meta( 'Token', true, $context );
	}

	public function get_debit_approve_number( $context = 'view' ): string {
		return $this->get_meta( 'DebitApproveNumber', true, $context );
	}

	public function get_token_object( Gateway $gateway ): \WC_Payment_Token_CC {
		$token = new \WC_Payment_Token_CC();
		$token->set_gateway_id( $gateway->id );
		$token->set_token( $this->get_token() );
		$token->set_last4( $this->get_last4() );
		$token->set_card_type( $this->get_card_type() );
		$token->set_expiry_year( $this->get_expiry_year() );
		$token->set_expiry_month( $this->get_expiry_month() );
		$token->set_user_id( $this->get_user_id() );

		return $token;
	}

	public function get_last4() {
		return substr( $this->get_card_number(), -4 );
	}

	public function get_card_number( $context = 'view' ): string {
		return $this->get_meta( 'CreditCardNumber', true, $context );
	}

	public function get_card_type(): string {
		$brand = $this->get_card_brand();
		$company = $this->get_card_company();

		return ( 0 < $brand )
			? $this->get_card_type_by_brand( $brand )
			: $this->get_card_type_by_company( $company );
	}

	public function get_card_brand( $context = 'view' ): int {
		return (int) $this->get_meta( 'CreditCardBrand', true, $context );
	}

	public function get_card_company( $context = 'view' ): int {
		return (int) $this->get_meta( 'CreditCardCompanyClearer', true, $context );
	}

	public function get_card_type_by_brand( $brand ) {
		$brands = apply_filters( 'wppc/card_brands', [
			1 => 'mastercard',
			2 => 'visa',
			3 => 'maestro',
			5 => 'isracard',
		] );

		return $brands[ $brand ] ?? $brand;
	}

	public function get_card_type_by_company( $company ) {
		$companies = apply_filters( 'wppc/card_companies', [
			1 => 'isracard',
			2 => 'visa',
			3 => 'diners',
			4 => 'american express',
			6 => 'leumi card',
		] );

		return $companies[ $company ] ?? $company;
	}

	public function get_expiry_year() {
		$expiry_year = substr( $this->get_card_expiry(), -2 );

		return date_create_from_format( 'y', $expiry_year )->format( 'Y' );
	}

	public function get_card_expiry( $context = 'view' ): string {
		return $this->get_meta( 'CreditCardExpDate', true, $context );
	}

	public function get_expiry_month() {
		return substr( $this->get_card_expiry(), 0, 2 );
	}

	public function get_total_payments( $context = 'view' ): int {
		$total_payments = (int) $this->get_meta( 'TotalPayments', true, $context );

		return $total_payments ?: 1;
	}

	public function is_success(): bool {
		return '000' === $this->get_status_code();
	}

	public function get_status_code( $context = 'view' ): string {
		return $this->get_prop( 'status_code', $context );
	}

	public function is_timeout(): bool {
		return 301 === (int) $this->get_status_code();
	}

	public function is_3ds_failure(): bool {
		return 650 === (int) $this->get_status_code();
	}

	public function get_3ds_params(): array {
		$params = array_filter( [
			$this->get_meta( 'Eci' ),
			$this->get_meta( 'XID' ),
			$this->get_meta( 'Cavv' ),
		] );

		return 3 === count( $params ) ? $params : [];
	}

	public function get_action_type( $context = 'view' ): string {
		return $this->get_meta( 'JParam', true, $context );
	}

	public function is_action_type( string $action_type ): bool {
		return $action_type === $this->get_action_type();
	}

	private static $is_get_transaction_request = false;

    public static function set_get_transaction_flag($flag) {
        self::$is_get_transaction_request = $flag;
    }

    public static function get_get_transaction_flag() {
        return self::$is_get_transaction_request;
    }
}

import java.io.IOException;
import java.net.URL;
import java.util.Scanner;

import org.json.JSONArray;
import org.json.JSONObject;

public class googleTrendstoSql {

	public static void main(String[] args) throws IOException {
		// TODO Auto-generated method stub

		// build a URL
		String s = "http://151.236.222.251/temp.json";
		URL url = new URL(s);

		// read from the URL
		Scanner scan = new Scanner(url.openStream());
		String str = new String();
		while (scan.hasNext())
			str += scan.nextLine();
		System.out.println(str);
		scan.close();

		// build a JSON object
		JSONObject obj = new JSONObject(str);

		JSONArray arr = obj.getJSONObject("table").getJSONArray("rows");
		System.out.println("there are elemts:" + arr.length());
		for (int i = 0; i < arr.length(); i++) {
			JSONArray post_array = arr.getJSONObject(i).getJSONArray("c");

			System.out
					.print("INSERT INTO win.google_raw (Date,Term,Value) VALUES ( ");

			for (int j = 0; j < post_array.length(); j++) {
				if (j == 0) {
					String post_id = post_array.getJSONObject(j).getString("v");
					String post_altered = post_id
							.replaceAll("DATE\\(", "DATE\\('")
							.replaceAll("\\)", "'\\)").replaceAll(",\\s", "-");
					if (post_altered.substring(12).length() == 4) {
						System.out.print("" + post_altered.substring(0, 11)
								+ ((i%12)+1) + post_altered.substring(12)
								+ ",'bitcoin'");
					} else {
						System.out.print("" + post_altered.substring(0, 11)
								+ ((i%12)+1) + post_altered.substring(13)
								+ ",'bitcoin'");
					}

				} else {
					String post_id = post_array.getJSONObject(j).getString("f");
					System.out.print("," + post_id);

				}
			}
			System.out.println(");");
		}

	}

}

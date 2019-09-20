import java.io.File;
import java.io.FileInputStream;
import java.io.PrintWriter;
import org.apache.tika.language.LanguageIdentifier;
import org.apache.tika.metadata.Metadata;
import org.apache.tika.parser.ParseContext;
import org.apache.tika.parser.html.HtmlParser;
import org.apache.tika.sax.BodyContentHandler;
public class Extraction{
public static void main(String args[]) throws Exception{
	PrintWriter op = new PrintWriter ("/Users/siri/IR/big.txt");
	String path = "/Users/siri/solr-7.5.0/mercury_news/mercurynew";
	String opath = "/Users/siri/solr-7.5.0/mercury_news/mercurynew/";
	String a;
	String b;
	File directory = new File(path);
	int cnt = 1;
	int num = 5;
	try 
	{
	for(File f: directory.listFiles())
	{
	b = f.getName();
	a = b.substring(0,b.length()-num);
	PrintWriter writer = new PrintWriter(opath + a);
	cnt++;
	BodyContentHandler handle = new BodyContentHandler(-1);
	Metadata meta = new Metadata();
	ParseContext context = new ParseContext();
	HtmlParser htmlparser = new HtmlParser();
	FileInputStream inpstream = new FileInputStream(f);
	htmlparser.parse(inpstream, handle, meta,context);
	String content = handle.toString();
	String w[] = content.split(" ");
	String cont = content.trim().replaceAll(" +", " ");
	String c = cont.replaceAll("[\r\n]+", "\n");
	writer.print(c);
	writer.close();
	for(String temp: w)
	{if(temp.matches("[a-zA-Z]+\\.?")){
	op.print(temp + " ");
	}}}} 
	catch (Exception msg) 
	{
	  msg.printStackTrace();
	}
	op.close();	
}
}
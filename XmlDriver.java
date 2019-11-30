import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.util.StringTokenizer;
import java.util.Calendar;
import java.util.concurrent.TimeUnit;
import javax.xml.stream.XMLInputFactory;
import javax.xml.stream.XMLStreamConstants;
import javax.xml.stream.XMLStreamReader;
import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.fs.FSDataInputStream;
import org.apache.hadoop.fs.FileSystem;
import org.apache.hadoop.fs.Path;
import org.apache.hadoop.io.DataOutputBuffer;
import org.apache.hadoop.io.LongWritable;
import org.apache.hadoop.io.IntWritable;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.InputSplit;
import org.apache.hadoop.mapreduce.Job;
import org.apache.hadoop.mapreduce.Mapper;
import org.apache.hadoop.mapreduce.RecordReader;
import org.apache.hadoop.mapreduce.Reducer;
import org.apache.hadoop.mapreduce.TaskAttemptContext;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.input.FileSplit;
import org.apache.hadoop.mapreduce.lib.input.TextInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;
import org.apache.hadoop.mapreduce.lib.output.TextOutputFormat;

public class XmlDriver
{

 public static class XmlInputFormat1 extends TextInputFormat {

    public static final String START_TAG_KEY = "xmlinput.start";
    public static final String END_TAG_KEY = "xmlinput.end";


    public RecordReader<LongWritable, Text> createRecordReader(
            InputSplit split, TaskAttemptContext context) {
        return new XmlRecordReader();
    }

    /**
     * XMLRecordReader class to read through a given xml document to output
     * xml blocks as records as specified by the start tag and end tag
     *
     */

    public static class XmlRecordReader extends
    RecordReader<LongWritable, Text> {
        private byte[] startTag;
        private byte[] endTag;
        private long start;
        private long end;
        private FSDataInputStream fsin;
        private DataOutputBuffer buffer = new DataOutputBuffer();

        private LongWritable key = new LongWritable();
        private Text value = new Text();
        @Override
        public void initialize(InputSplit split, TaskAttemptContext context)
        throws IOException, InterruptedException {
            Configuration conf = context.getConfiguration();
            startTag = conf.get(START_TAG_KEY).getBytes("utf-8");
            endTag = conf.get(END_TAG_KEY).getBytes("utf-8");
            FileSplit fileSplit = (FileSplit) split;

            // open the file and seek to the start of the split
            start = fileSplit.getStart();
            end = start + fileSplit.getLength();
            Path file = fileSplit.getPath();
            FileSystem fs = file.getFileSystem(conf);
            fsin = fs.open(fileSplit.getPath());
            fsin.seek(start);

        }
        @Override
        public boolean nextKeyValue() throws IOException,
        InterruptedException {
            if (fsin.getPos() < end) {
                if (readUntilMatch(startTag, false)) {
                    try {
                        buffer.write(startTag);
                        if (readUntilMatch(endTag, true)) {
                            key.set(fsin.getPos());
                            value.set(buffer.getData(), 0,
                                    buffer.getLength());
                            return true;
                        }
                    } finally {
                        buffer.reset();
                    }
                }
            }
            return false;
        }
        @Override
        public LongWritable getCurrentKey() throws IOException,
        InterruptedException {
            return key;
        }

        @Override
        public Text getCurrentValue() throws IOException,
        InterruptedException {
            return value;
        }
        @Override
        public void close() throws IOException {
            fsin.close();
        }
        @Override
        public float getProgress() throws IOException {
            return (fsin.getPos() - start) / (float) (end - start);
        }

        private boolean readUntilMatch(byte[] match, boolean withinBlock)
        throws IOException {
            int i = 0;
            while (true) {
                int b = fsin.read();
                // end of file:
                    if (b == -1)
                        return false;
                // save to buffer:
                    if (withinBlock)
                        buffer.write(b);
                // check if we're matching:
                    if (b == match[i]) {
                        i++;
                        if (i >= match.length)
                            return true;
                    } else
                        i = 0;
                    // see if we've passed the stop point:
                    if (!withinBlock && i == 0 && fsin.getPos() >= end)
                        return false;
            }
        }
    }
}


public static class Map extends Mapper<LongWritable, Text,
Text, Text> {
    private final static IntWritable one = new IntWritable(1);
    private Text word = new Text();
    @Override
    protected void map(LongWritable key, Text value,
            Mapper.Context context)
    throws
    IOException, InterruptedException {
        String document = value.toString();
        try {
            XMLStreamReader reader =
                XMLInputFactory.newInstance().createXMLStreamReader(new
                        ByteArrayInputStream(document.getBytes()));
            String propertyValue = "";
	          String finalValue = "";
            String currentElement = "";
            while (reader.hasNext()) {
                int code = reader.next();

                switch (code) {
                case XMLStreamConstants.START_ELEMENT: //START_ELEMENT:
                    currentElement = reader.getLocalName();
                    break;
                case XMLStreamConstants.CHARACTERS: //CHARACTERS:
                    if (currentElement.equalsIgnoreCase("title")) {
                        propertyValue += reader.getText();
                    } else if (currentElement.equalsIgnoreCase("text")) {
                        propertyValue += reader.getText();
                    } else if (currentElement.equalsIgnoreCase("username")) {
                        propertyValue += reader.getText();
                    }
                    break;
                }
            }
            reader.close();
      	    finalValue = propertyValue.replaceAll("[^A-Za-z0-9 ]", " ");
      	    finalValue = finalValue.toLowerCase();
      	    StringTokenizer itr = new StringTokenizer(finalValue.trim());
      	    while (itr.hasMoreTokens()) {
      		      word.set(itr.nextToken());
      		      context.write(word, one);
      	    }
          }
      catch(Exception e){
          throw new IOException(e);
      }
    }
}

 public static class Reduce
    extends Reducer<Text,IntWritable,Text,IntWritable> {
    private IntWritable result = new IntWritable();
    public void reduce(Text key, Iterable<IntWritable> values,
                       Context context
                       ) throws IOException, InterruptedException {
      int sum = 0;
      for (IntWritable val : values) {
        sum += val.get();
      }
      result.set(sum);
      context.write(key, result);
    }
  }

public static String getDurationBreakdown(long millis) {
        if(millis < 0) {
            throw new IllegalArgumentException("Duration must be greater than zero!");
        }

        long hours = TimeUnit.MILLISECONDS.toHours(millis);
        millis -= TimeUnit.HOURS.toMillis(hours);
        long minutes = TimeUnit.MILLISECONDS.toMinutes(millis);
        millis -= TimeUnit.MINUTES.toMillis(minutes);
        long seconds = TimeUnit.MILLISECONDS.toSeconds(millis);

        StringBuilder sb = new StringBuilder(64);
        sb.append(hours);
        sb.append(" Hours ");
        sb.append(minutes);
        sb.append(" Minutes ");
        sb.append(seconds);
        sb.append(" Seconds");

        return(sb.toString());
    }

public static void main(String[] args) throws Exception
{
    Long startTime = Calendar.getInstance().getTimeInMillis();
    Configuration conf = new Configuration();

    conf.set("xmlinput.start", "<page>");
    conf.set("xmlinput.end", "</page>");
    Job job = new Job(conf);
    job.setJarByClass(XmlDriver.class);
    job.setOutputKeyClass(Text.class);
    job.setOutputValueClass(IntWritable.class);

    job.setMapperClass(XmlDriver.Map.class);
    job.setReducerClass(XmlDriver.Reduce.class);

    job.setInputFormatClass(XmlInputFormat1.class);
    job.setOutputFormatClass(TextOutputFormat.class);

    FileInputFormat.addInputPath(job, new Path(args[0]));
    FileOutputFormat.setOutputPath(job, new Path(args[1]));

    job.waitForCompletion(true);
    Long endTime = Calendar.getInstance().getTimeInMillis();
    Long sumTime = endTime-startTime;
    System.out.println("Duration: " + getDurationBreakdown(sumTime));
  }
}

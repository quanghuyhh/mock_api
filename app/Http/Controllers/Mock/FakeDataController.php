<?php

namespace App\Http\Controllers\Mock;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Support\Facades\DB;
use App\Models\Author;
use App\Models\MetaDataType;
use App\Models\MetaData;
use App\Models\Section;
use App\Models\SectionItem;
use App\Models\Summary;
use App\Models\Category;
use App\Models\Text;
use App\Models\Audio;
use App\Models\Video;

class FakeDataController extends Controller
{
    public function summary() {
        DB::beginTransaction();
        try {
            Text::query()->truncate();
            Audio::query()->truncate();
            Video::query()->truncate();
            
            $faker = \Faker\Factory::create();
            foreach (range(1, 100) as $item) {
                $textInfo = new Text();
                $textInfo->fill([
                    'url' => url('/sample/book/sample.citadel'),
                    'duration' => 12345
                ])->save();

                $audioInfo = new Audio();
                $audioInfo->fill([
                    'duration' => 45,
                    'url' => url('/sample/audio/sample.mp3')
                ])->save();

                $videoInfo = new Video();
                $videoInfo->fill([
                    'duration' => 5,
                    'url' => url('/sample/video/sample.mp4'),
                    'thumb_url' => url('/sample/video/thumb.png')
                ])->save();

                $summary = new Summary();
                $summary->fill([
                    'text_id' => $textInfo->id,
                    'audio_id' => $audioInfo->id,
                    'video_id' => $videoInfo->id,
                ])->save();
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            dump($exception->getMessage());
        }
    }

    public function books() {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \DavidBadura\FakerMarkdownGenerator\FakerProvider($faker));
        
        $cover1 = new \stdClass();
        $cover1->original = "https://images-na.ssl-images-amazon.com/images/I/41FNZJgQbLL._SX333_BO1,204,203,200_.jpg";
        $cover1->small = "https://images-na.ssl-images-amazon.com/images/I/41FNZJgQbLL._SX333_BO1,204,203,200_.jpg";
        $cover1->medium = "https://images-na.ssl-images-amazon.com/images/I/41FNZJgQbLL._SX333_BO1,204,203,200_.jpg";
        $cover1->large = "https://images-na.ssl-images-amazon.com/images/I/41FNZJgQbLL._SX333_BO1,204,203,200_.jpg";

        $markdown = "## From Publishers Weekly\nIn what Collins terms a prequel to the bestseller Built to Last he wrote with Jerry Porras, this worthwhile effort explores the way good organizations can be turned into ones that produce great, sustained results. To find the keys to greatness, Collins's 21-person research team (at his management research firm) read and coded 6,000 articles, generated more than 2,000 pages of interview transcripts and created 384 megabytes of computer data in a five-year project. That Collins is able to distill the findings into a cogent, well-argued and instructive guide is a testament to his writing skills. After establishing a definition of a good-to-great transition that involves a 10-year fallow period followed by 15 years of increased profits, Collins's crew combed through every company that has made the Fortune 500 (approximately 1,400) and found 11 that met their criteria, including Walgreens, Kimberly Clark and Circuit City. At the heart of the findings about these companies' stellar successes is what Collins calls the Hedgehog Concept, a product or service that leads a company to outshine all worldwide competitors, that drives a company's economic engine and that a company is passionate about. While the companies that achieved greatness were all in different industries, each engaged in versions of Collins's strategies. While some of the overall findings are counterintuitive (e.g., the most effective leaders are humble and strong-willed rather than outgoing), many of Collins's perspectives on running a business are amazingly simple and commonsense. This is not to suggest, however, that executives at all levels wouldn't benefit from reading this book; after all, only 11 companies managed to figure out how to change their B grade to an A on their own.\n\n## More about the author\nJim Collins is a student and teacher of what makes great companies tick, and a Socratic advisor to leaders in the business and social sectors. Having invested more than a quarter century in rigorous research, he has authored or coauthored six books that have sold in total more than 10 million copies worldwide. They include Good to Great, the #1 bestseller, which examines why some companies make the leap to superior results, along with its companion work Good to Great and the Social Sectors; the enduring classic Built to Last, which explores how some leaders build companies that remain visionary for generations; How the Mighty Fall, which delves into how once-great companies can self-destruct; and Great by Choice, which is about thriving in chaos—why some do, and others don't.";
        $authors = Author::all();
        $metaDatas = MetaData::all();
        $summaries = Summary::all();
        $categories = Category::all();
        
        DB::beginTransaction();
        try {
            Book::query()->truncate();
            foreach (range(0, 100) as $item) {
                $book = new Book();
                $book->fill([
                    "title" => $faker->name(),
                    "short_description" => $faker->paragraphs(3, true),
                    "overview_markdown" => $markdown,
                    "cover_info" => json_encode($cover1)
                ])->save();

                $book->authors()->sync($authors->random(3)->pluck('id'));
                $book->metas()->sync($metaDatas->random(3)->pluck('id'));
                $book->summaries()->sync($summaries->random(3)->pluck('id'));
                $book->categories()->sync($categories->random(3)->pluck('id'));
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            dump($exception, $exception->getMessage());
        }
    }

    public function authors() {
        $faker = \Faker\Factory::create();

        DB::beginTransaction();
        try {
            foreach (range(0, 100) as $item) {
                $autor = new Author();
                $autor->fill(['name' => $faker->name ])->save();
            }
            
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            dump($exception->getMessage());
        }
    }

    public function types() {
        $faker = \Faker\Factory::create();
        $types = [
            'Reading Time', 'GoodReads', 'Release'
        ];
        DB::beginTransaction();
        try {
            MetaDataType::query()->truncate();
            foreach ($types as $name) {
                $type = new MetaDataType();
                $type->fill(['title' => $name ])->save();
            }
            
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            dump($exception->getMessage());
        }
    }

    public function metas() {
        $faker = \Faker\Factory::create();
        DB::beginTransaction();
        $types = MetaDataType::query()->count();

        try {
            MetaData::query()->truncate();
            foreach (range(0, 100) as $item) {
                $meta = new MetaData();
                $meta->fill([
                    'value' => $faker->numberBetween(100, 1000),
                    'type_id' => $faker->numberBetween(1, $types)
                ])->save();
            }
            
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            dump($exception->getMessage());
        }
    }

    public function sectionsOld() {
        DB::beginTransaction();
        $data = '{"sections":[{"sectionId":"section1section1","title":"Recommended","subtitle":"Base on your reading history","sectionItemType":"book","layoutType":"listHorizontal","sectionItems":[{"objectId":"b1","title":"Radical Candor","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41niRlvb2PL._SX327_BO1,204,203,200_.jpg","authorName":"Kim Scott","shortDescription":"","readingProgress":0},{"objectId":"b2","title":"How to Lead When You\'re Not in Charge","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51Zon0zoGDL._SX326_BO1,204,203,200_.jpg","authorName":"Clay Scroggins","shortDescription":"","readingProgress":0},{"objectId":"b3","title":"Educated: A Memoir","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41Ld1sqdhgL.jpg","authorName":"Tara Westover","shortDescription":"","readingProgress":0},{"objectId":"b4","title":"Becoming","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/414JfiBCutL.jpg","authorName":"Michelle Obama","shortDescription":"","readingProgress":0},{"objectId":"b5","title":"The Moment of Lift","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41W9YpbHNbL.jpg","authorName":"Melinda Gates","shortDescription":"","readingProgress":0}]},{"sectionId":"section2","title":"Reading","subtitle":"Back to your current progress","sectionItemType":"book","layoutType":"listHorizontalReading","sectionItems":[{"objectId":"b1","title":"The Second Mountain","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41SzC-0E6hL.jpg","authorName":"David Brooks","shortDescription":"","readingProgress":60},{"objectId":"b2","title":"The Road to Character","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41iR5adHBpL.jpg","authorName":"David Brooks","shortDescription":"","readingProgress":60},{"objectId":"b1","title":"The Hacking of the American Mind","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51M9AEzfG0L.jpg","authorName":"Robert H. Lustig","shortDescription":"","readingProgress":60},{"objectId":"b1","title":"Cure: A Journey into the Science of Mind Over Body","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41GA2sPZWrL._SX348_BO1,204,203,200_.jpg","authorName":"Jo Marchant","shortDescription":"","readingProgress":60}]},{"sectionId":"section3","title":"Top Read This Week","subtitle":"See what the world is reading","sectionItemType":"book","layoutType":"gridHorizontalTopChart","sectionItems":[{"objectId":"b1","title":"The Superhuman Mind","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51MU2nrbAWL.jpg","authorName":"Berit Brogaard PhD","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Mental Models","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51O-bC9q4TL.jpg","authorName":"Peter Hollins","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Words That Change Minds","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41wXENbiq5L.jpg","authorName":"Shelle Rose Charvet","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Mind to Matter","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51HgDAguzHL.jpg","authorName":"Dawson Church","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"The Leadership Code","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41REul0ljkL.jpg","authorName":"Britton Costa","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"The Scribe Method","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41R-V3JddqL.jpg","authorName":"Tucker Max","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Self-Discipline for Writers","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41IcUHwXlfL.jpg","authorName":"Martin Meadows","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"The Science of Getting Started","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41TwXuRlK9L.jpg","authorName":"Patrick King","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"How to Write Your First Book","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41dz49kq73L.jpg","authorName":"D Arlando Fortune","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Executive Freedom","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41iGTBUnL8L.jpg","authorName":"Colin Mills","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Your Wow Years!","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51iMXsiBtPL.jpg","authorName":"Rita Connor","shortDescription":"","readingProgress":0}]},{"sectionId":"section4","title":"Featured","subtitle":"","sectionItemType":"book","layoutType":"singleFeatured","sectionItems":[{"objectId":"b1","title":"Intelligent Thinking","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51L-sE%2BghQL.jpg","authorName":"Som Bathla","shortDescription":"This book will equip your mental tool box with some highly effective ways to help you think better, make better decisions and solve any problems. I found some pretty effective tips.\n","readingProgress":0}]},{"sectionId":"section5","title":"The NYT Best Sellers","subtitle":"This month best selling books","sectionItemType":"book","layoutType":"listHorizontal","sectionItems":[{"objectId":"b1","title":"You Are Not So Smart","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51G70KBZyLL.jpg","authorName":"David McRaney","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Grad School Essentials","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51z3O6WsJOL._SX332_BO1,204,203,200_.jpg","authorName":"Zachary Shore","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"The Knowledge Illusion","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41nj2wYvSGL.jpg","authorName":"Steven Sloman","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Data and Goliath","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51rddzqd88L._SX330_BO1,204,203,200_.jpg","authorName":"Bruce Schneier","shortDescription":"","readingProgress":0}]},{"sectionId":"section6","title":"Tim Ferris Recommended","subtitle":"On his podcast channel","sectionItemType":"book","layoutType":"listHorizontal","sectionItems":[{"objectId":"b1","title":"Liars and Outliers","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51yllus4s-L.jpg","authorName":"Bruce Schneier","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Smart But Stuck","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41aZMpa3HNL._SX333_BO1,204,203,200_.jpg","authorName":"Thomas E. Brown","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Your Life Can Be Better","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51WwZxfH8BL.jpg","authorName":"Douglas A. Puryear MD","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Book 1","thumbImageUrl":"https://d188rgcu4zozwl.cloudfront.net/content/B01B3DKROQ/resources/1811288692","authorName":"Daniel Kahneman","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Book 1","thumbImageUrl":"https://d188rgcu4zozwl.cloudfront.net/content/B01B3DKROQ/resources/1811288692","authorName":"Daniel Kahneman","shortDescription":"","readingProgress":0}]},{"sectionId":"section7section7","title":"Categories","subtitle":"","sectionItemType":"category","layoutType":"listVertical","sectionItems":[{"objectId":"category-2category-2","title":"Business","thumbImageUrl":"https://i.ibb.co/KVSbFGM/category-life-social.png","authorName":"","shortDescription":"","readingProgress":0},{"objectId":"category-2category-2","title":"Social & Life","thumbImageUrl":"https://i.ibb.co/DMwy465/category-productivity.png","authorName":"","shortDescription":"","readingProgress":0},{"objectId":"category-2category-2","title":"Productivity","thumbImageUrl":"https://i.ibb.co/NYzPbQ1/category-psychology.png","authorName":"","shortDescription":"","readingProgress":0},{"objectId":"category-2category-2","title":"Psychology","thumbImageUrl":"https://i.ibb.co/th2Ljtp/category-business.png","authorName":"","shortDescription":"","readingProgress":0}]}]}';
        $list = json_decode($data);

        $insertSections = [];
        $insertItems = [];
        $_items = [];
        foreach ($list->sections as $section) {
            $section_key = str_slug($section->title);
            $_sectionItems = [];
            foreach ($section->sectionItems as $sectionItem) {
                $item_key = str_slug($sectionItem->title);
                $_sectionItems[$item_key] = $sectionItem;
                $insertItems[$item_key] = $sectionItem;
            }

            $_items[$section_key] = $_sectionItems;
            $insertSections[$section_key] = $section;
        }
        

        $sectionLayouts = array_flip(get_list_section_layout());
        $sectionTypes = array_flip(get_list_section_type());

        try {
            SectionItem::query()->truncate();
            Section::query()->truncate();

            foreach($insertItems as $insertItem) {
                $itemData = [
                    'title' => $insertItem->title,
                    'thumb_image_url' => $insertItem->thumbImageUrl,
                    'author_name' => $insertItem->authorName,
                    'short_description' => $insertItem->shortDescription,
                    'reading_progress' => $insertItem->readingProgress
                ];

                $_it = new SectionItem();
                $_it->fill($itemData)->save();
            }

            $dbItems = SectionItem::all();
            foreach ($insertSections as $key => $insertSection) {
                $itemKeys = $dbItems->map(function($mapItem) {
                    $mapItem->localKey = str_slug($mapItem->title);

                    return $mapItem;
                })->whereIn('localKey', array_keys($_items[$key]))->pluck('id');
                
                $sectionData = [
                    'title' => $insertSection->title,
                    'subtitle' => $insertSection->subtitle,
                    'section_item_type' => $sectionTypes[$insertSection->sectionItemType],
                    'layout_type' => $sectionLayouts[$insertSection->layoutType],
                ];
                $section = new Section();
                $section->fill($sectionData)->save();
                $section->items()->sync($itemKeys->toArray());
            }
            
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            dump($exception->getMessage());
        }
    }

    public function sections() {
        DB::beginTransaction();
        $data = '{"sections":[{"sectionId":"section1section1","title":"Recommended","subtitle":"Base on your reading history","sectionItemType":"book","layoutType":"listHorizontal","sectionItems":[{"objectId":"b1","title":"Radical Candor","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41niRlvb2PL._SX327_BO1,204,203,200_.jpg","authorName":"Kim Scott","shortDescription":"","readingProgress":0},{"objectId":"b2","title":"How to Lead When You\'re Not in Charge","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51Zon0zoGDL._SX326_BO1,204,203,200_.jpg","authorName":"Clay Scroggins","shortDescription":"","readingProgress":0},{"objectId":"b3","title":"Educated: A Memoir","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41Ld1sqdhgL.jpg","authorName":"Tara Westover","shortDescription":"","readingProgress":0},{"objectId":"b4","title":"Becoming","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/414JfiBCutL.jpg","authorName":"Michelle Obama","shortDescription":"","readingProgress":0},{"objectId":"b5","title":"The Moment of Lift","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41W9YpbHNbL.jpg","authorName":"Melinda Gates","shortDescription":"","readingProgress":0}]},{"sectionId":"section2","title":"Reading","subtitle":"Back to your current progress","sectionItemType":"book","layoutType":"listHorizontalReading","sectionItems":[{"objectId":"b1","title":"The Second Mountain","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41SzC-0E6hL.jpg","authorName":"David Brooks","shortDescription":"","readingProgress":60},{"objectId":"b2","title":"The Road to Character","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41iR5adHBpL.jpg","authorName":"David Brooks","shortDescription":"","readingProgress":60},{"objectId":"b1","title":"The Hacking of the American Mind","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51M9AEzfG0L.jpg","authorName":"Robert H. Lustig","shortDescription":"","readingProgress":60},{"objectId":"b1","title":"Cure: A Journey into the Science of Mind Over Body","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41GA2sPZWrL._SX348_BO1,204,203,200_.jpg","authorName":"Jo Marchant","shortDescription":"","readingProgress":60}]},{"sectionId":"section3","title":"Top Read This Week","subtitle":"See what the world is reading","sectionItemType":"book","layoutType":"gridHorizontalTopChart","sectionItems":[{"objectId":"b1","title":"The Superhuman Mind","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51MU2nrbAWL.jpg","authorName":"Berit Brogaard PhD","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Mental Models","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51O-bC9q4TL.jpg","authorName":"Peter Hollins","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Words That Change Minds","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41wXENbiq5L.jpg","authorName":"Shelle Rose Charvet","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Mind to Matter","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51HgDAguzHL.jpg","authorName":"Dawson Church","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"The Leadership Code","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41REul0ljkL.jpg","authorName":"Britton Costa","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"The Scribe Method","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41R-V3JddqL.jpg","authorName":"Tucker Max","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Self-Discipline for Writers","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41IcUHwXlfL.jpg","authorName":"Martin Meadows","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"The Science of Getting Started","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41TwXuRlK9L.jpg","authorName":"Patrick King","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"How to Write Your First Book","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41dz49kq73L.jpg","authorName":"D Arlando Fortune","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Executive Freedom","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41iGTBUnL8L.jpg","authorName":"Colin Mills","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Your Wow Years!","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51iMXsiBtPL.jpg","authorName":"Rita Connor","shortDescription":"","readingProgress":0}]},{"sectionId":"section4","title":"Featured","subtitle":"","sectionItemType":"book","layoutType":"singleFeatured","sectionItems":[{"objectId":"b1","title":"Intelligent Thinking","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51L-sE%2BghQL.jpg","authorName":"Som Bathla","shortDescription":"This book will equip your mental tool box with some highly effective ways to help you think better, make better decisions and solve any problems. I found some pretty effective tips.\n","readingProgress":0}]},{"sectionId":"section5","title":"The NYT Best Sellers","subtitle":"This month best selling books","sectionItemType":"book","layoutType":"listHorizontal","sectionItems":[{"objectId":"b1","title":"You Are Not So Smart","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51G70KBZyLL.jpg","authorName":"David McRaney","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Grad School Essentials","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51z3O6WsJOL._SX332_BO1,204,203,200_.jpg","authorName":"Zachary Shore","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"The Knowledge Illusion","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41nj2wYvSGL.jpg","authorName":"Steven Sloman","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Data and Goliath","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51rddzqd88L._SX330_BO1,204,203,200_.jpg","authorName":"Bruce Schneier","shortDescription":"","readingProgress":0}]},{"sectionId":"section6","title":"Tim Ferris Recommended","subtitle":"On his podcast channel","sectionItemType":"book","layoutType":"listHorizontal","sectionItems":[{"objectId":"b1","title":"Liars and Outliers","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51yllus4s-L.jpg","authorName":"Bruce Schneier","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Smart But Stuck","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/41aZMpa3HNL._SX333_BO1,204,203,200_.jpg","authorName":"Thomas E. Brown","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Your Life Can Be Better","thumbImageUrl":"https://images-na.ssl-images-amazon.com/images/I/51WwZxfH8BL.jpg","authorName":"Douglas A. Puryear MD","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Book 1","thumbImageUrl":"https://d188rgcu4zozwl.cloudfront.net/content/B01B3DKROQ/resources/1811288692","authorName":"Daniel Kahneman","shortDescription":"","readingProgress":0},{"objectId":"b1","title":"Book 1","thumbImageUrl":"https://d188rgcu4zozwl.cloudfront.net/content/B01B3DKROQ/resources/1811288692","authorName":"Daniel Kahneman","shortDescription":"","readingProgress":0}]},{"sectionId":"section7section7","title":"Categories","subtitle":"","sectionItemType":"category","layoutType":"listVertical","sectionItems":[{"objectId":"category-2category-2","title":"Business","thumbImageUrl":"https://i.ibb.co/KVSbFGM/category-life-social.png","authorName":"","shortDescription":"","readingProgress":0},{"objectId":"category-2category-2","title":"Social & Life","thumbImageUrl":"https://i.ibb.co/DMwy465/category-productivity.png","authorName":"","shortDescription":"","readingProgress":0},{"objectId":"category-2category-2","title":"Productivity","thumbImageUrl":"https://i.ibb.co/NYzPbQ1/category-psychology.png","authorName":"","shortDescription":"","readingProgress":0},{"objectId":"category-2category-2","title":"Psychology","thumbImageUrl":"https://i.ibb.co/th2Ljtp/category-business.png","authorName":"","shortDescription":"","readingProgress":0}]}]}';
        $list = json_decode($data);

        $insertSections = [];
        $insertItems = [];
        $_items = [];
        foreach ($list->sections as $section) {
            $section_key = str_slug($section->title);
            $_sectionItems = [];
            foreach ($section->sectionItems as $sectionItem) {
                $item_key = str_slug($sectionItem->title);
                $_sectionItems[$item_key] = $sectionItem;
                $insertItems[$item_key] = $sectionItem;
            }

            $_items[$section_key] = $_sectionItems;
            $insertSections[$section_key] = $section;
        }
        

        $sectionLayouts = array_flip(get_list_section_layout());
        $sectionTypes = array_flip(get_list_section_type());

        try {

            $books = Book::all();
            $categories = Category::all();

            SectionItem::query()->truncate();
            Section::query()->truncate();

            foreach ($insertSections as $key => $insertSection) {
                $sectionData = [
                    'title' => $insertSection->title,
                    'subtitle' => $insertSection->subtitle,
                    'section_item_type' => $sectionTypes[$insertSection->sectionItemType],
                    'layout_type' => $sectionLayouts[$insertSection->layoutType],
                ];
                $section = new Section();
                $section->fill($sectionData)->save();

                $itemNums = get_list_section_items($section->layout_type);
                if ($section->section_item_type == SECTION_TYPE_BOOK) {
                    $itemIds = $books->random($itemNums)->pluck('id');
                    $section->books()->sync($itemIds);
                } else {
                    $itemIds = $categories->random($itemNums)->pluck('id');
                    $section->categories()->sync($itemIds);
                }
            }
            
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            dump($exception->getMessage());
        }
    }

    public function categories() {
        Category::query()->truncate();
        $categories = ["Arts & Music", "Biographies", "Business", "Kids", "Comics", "Computers & Tech", "Cooking", "Hobbies & Crafts", "Edu & Reference", "Gay & Lesbian", "Health & Fitness", "History", "Home & Garden", "Horror", "Horror", "Entertainment", "Literature & Fiction", "Medical", "Medical", "Mysteries", "Parenting", "Social Sciences", "Religion", "Romance", "Science & Math", "Sci-Fi & Fantasy", "Self-Help", "Sports", "Teen", "Travel", "True Crime", "Westerns", 
        "Music","Painting","Performing Arts","Photography","Sculpture","Shop All","Ethnic & Cultural","Europe","Historical","Leaders & Notable People","Military","Shop All","Finance","Economics","Industries","International","Careers","Shop All","Animals","Action & Adventure","Activities, Crafts & Games","Activity Books","Cars & Trains","Shop All","Comic Books","Comic Strips","Dark Horse","DC Comics","Fantasy","Shop All","Apple","CAD","Certification","Computer Science","Databases","Shop All","Baking","BBQ","Preserving","Culinary Arts","Desserts","Shop All","Antiques","Clay","Collecting","Fashion","Jewelry Making","Shop All","Catalogs","Colleges","Curriculum","Dictionaries","Education","Shop All","Biographies","GLBT Books","Family","History","Literature & Fiction","Shop All","Aging","Alternative Medicine","Beauty, Grooming & Style","Children's Health","Disease & Ailments","Shop All","Medieval","Middle Eastern","Military","Modern","Native American","Shop All","Architecture","Flowers","Fruit","Home Decorating","Home Improvement","Shop All","Ghosts","Paranormal","Supernatural","Vampires","Zombies","Shop All","Ghosts","Paranormal","Supernatural","Vampires","Zombies","Shop All","Entertainers","Games","Humor","Brain Teasers","Movies","Shop All","Foreign Language","Genre Fiction","History & Criticism","Poetry","World Literature","Shop All","Administration","Allied Health","Basic Sciences","Clinical","Dentistry","Shop All","Administration","Allied Health","Basic Sciences","Clinical","Dentistry","Shop All","Conspiracy","Crime","Detective","Mysteries","Suspense","Shop All","Family Relationships","Fertility","Infants","Pregnancy & Childbirth","Special Needs","Shop All","Anarchy","Canadian Politics","Civil Rights","Socialism","Comparative Politics","Shop All","Agnosticism","Astrology","Atheism","Buddhism","Christian","Shop All","Time Travel","Vampires","Western","Contemporary","Erotica","Shop All","Agricultural Sciences","Anatomy","Animals","Astronomy","Biology","Shop All","Role Playing","Anthologies","Horror","Humor","Space","Shop All","Abuse","Addictions","Anger Management","Anxieties & Phobias","Social Skills","Shop All","Soccer","Training","Survival Skills","Water Sports","Winter Sports","Shop All","Being a Teen","Fantasy","Historical Fiction","Hobbies","Horror","Shop All","Africa","Asia","Canada","Caribbean","Europe","Shop All","Famous Criminals","Murder & Mayhem","Organized Crime","Serial Killers","Criminal Law","Shop All","Native American","Humor","Prairie Settlement","Cowboys","Gold Rush","Shop All"];

        foreach ($categories as $name) {
            $cate = new Category();
            $cate->fill(['title' => $name])->save();
        }
    }

    public function bookCates() {
        $books = Book::all();
        $categories = Category::all();

        DB::beginTransaction();
        try {
            DB::table('book_categories')->truncate();
            
            foreach ($categories as $category) {
                if ($category->books->isEmpty())
                    $category->books()->sync($books->random(10)->pluck('id'));
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            dump($exception->getMessage());
        }

    }

    public function mockall() {
        $this->summary();
        $this->authors();
        $this->types();
        $this->metas();
        $this->categories();
        $this->books();
        $this->sections();
    }
}

#!/usr/bin/python

import sys
import csv
import MySQLdb
import json

def dbconnect_fordata(cur):

    cur


    # Use all the SQL you like
    #cur.execute("SELECT Content FROM twitter_raw")

    cur.execute("""SELECT ID,Content FROM twitter_raw
         WHERE value is NULL LIMIT 1000""")

    twitterreader = cur.fetchall()

    # for line in twitterreader:
    #      twitter_list_dict.append(line[0])
    # return twitter_list_dict




    return twitterreader

# def tweet_dict(twitterData):  
#     ''' (file) -> list of dictionaries
#     This method should take your .csv
#     file and create a list of dictionaries.
#     '''
#     twitter_list_dict = []
#     twitterfile = open(twitterData)
#     twitterreader = csv.reader(twitterfile)
#     for line in twitterreader:
#         twitter_list_dict.append(line[0])
#     return twitter_list_dict


def sentiment_dict(sentimentData):
    ''' (file) -> dictionary
    This method should take your sentiment file
    and create a dictionary in the form {word: value}
    '''
    afinnfile = open(sentimentData)
    scores = {} # initialize an empty dictionary
    for line in afinnfile:
        term, score  = line.split("\t")  # The file is tab-delimited. "\t" means "tab character"
        scores[term] = float(score)  # Convert the score to an integer.
       
    return scores # Print every (term, score) pair in the dictionary

def main():

    #twitterData = sys.argv[1] #csv file
    with open("../db.json", "r") as f:
        data = json.loads(f.read())

    db = MySQLdb.connect(
    host=data["host"], # your host, usually localhost
    user=data["user"], # your username
    passwd=data["password"], # your password
    db=data["dbname"]) # name of the data base

    # you must create a Cursor object. It will let
    #  you execute all the queries you need
    cur = db.cursor() 

    tweets = dbconnect_fordata(cur)
    sentiment = sentiment_dict("AFINN-111.txt")
    
    """Calculate sentiment scores for the whole tweet with unknown terms set to score of zero
    then accumulates a dictionary of list of values: new term -> new entry that has the word as key.
    """
    for tweet_word in tweets:

        print tweet_word
        exit
        
        #print tweets[index]
        #tweet_word = tweets[index].split()
        sent_score = 0 # sentiment score della frase
        #tweet_word.join(tweet_word)
        
        tweet_id = tweet_word[0]
        
        indiword = tweet_word[1].split(" ")
        for oneword in indiword:

            oneword = oneword.rstrip('?:!.,;"!@')
            oneword = oneword.replace("\n", "")
            oneword = oneword.decode('utf-8')

            if not (oneword.encode('utf-8', 'ignore') == ""):
                if oneword.encode('utf-8') in sentiment.keys():
                    sent_score = sent_score + float(sentiment[oneword])
                    
        #if(sent_score != )
        #word = tweet_word[0].replace("\n", "")
        #print word + " --- "+ str(sent_score)



        print tweet_id, sent_score, "\n"
        
        # print("foo %d, bar %d" % (1,2))
        print("UPDATE twitter_raw SET Value = %s WHERE ID = %s " % (sent_score, tweet_id ))
        cur.execute("""UPDATE twitter_raw SET Value = %s WHERE ID ='%s' """ % (sent_score, tweet_id ))
                # cur.execute("UPDATE twitter_blapp SET Value = %s WHERE ID = %s " , (sent_score, tweet_id ))
        # cur.execute("UPDATE twitter_blapp SET Value = ? WHERE Content = ? ", (str(sent_score), str(tweet_word[0])))
        db.commit()
    
    cur = db.close()

        # mysqli_query($con,"UPDATE Persons SET Age=36
        # WHERE FirstName='Peter' AND LastName='Griffin'");

if __name__ == '__main__':
    main()

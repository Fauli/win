#!/usr/bin/python

import numpy as np
import MySQLdb
import json

def readSentimentList(file_name):
    ifile = open(file_name, 'r')
    happy_log_probs = {}
    sad_log_probs = {}
    ifile.readline() #Ignore title row
    
    for line in ifile:
        tokens = line[:-1].split(',')
        happy_log_probs[tokens[0]] = float(tokens[1])
        sad_log_probs[tokens[0]] = float(tokens[2])

    return happy_log_probs, sad_log_probs

def classifySentiment(words, happy_log_probs, sad_log_probs):
    # Get the log-probability of each word under each sentiment
    happy_probs = [happy_log_probs[word] for word in words if word in happy_log_probs]
    sad_probs = [sad_log_probs[word] for word in words if word in sad_log_probs]

    # Sum all the log-probabilities for each sentiment to get a log-probability for the whole tweet
    tweet_happy_log_prob = np.sum(happy_probs)
    tweet_sad_log_prob = np.sum(sad_probs)

    # Calculate the probability of the tweet belonging to each sentiment
    prob_happy = np.reciprocal(np.exp(tweet_sad_log_prob - tweet_happy_log_prob) + 1)
    prob_sad = 1 - prob_happy

    return prob_happy, prob_sad


def main():
    # We load in the list of words and their log probabilities
    # data1
    happy_log_probs, sad_log_probs = readSentimentList('twitter_sentiment_list.csv')
    # data2
    #happy_log_probs, sad_log_probs = readSentimentList('AFINNData')

    # Get Tweets and tokenize them
    with open("../../db.json", "r") as f:
        data = json.loads(f.read())

    db = MySQLdb.connect(
    host=data["host"], # your host, usually localhost
    user=data["user"], # your username
    passwd=data["password"], # your password
    db=data["dbname"]) # name of the data base

    # you must create a Cursor object. It will let
    #  you execute all the queries you need
    cur = db.cursor() 

    # Use all the SQL you like
    cur.execute("SELECT * FROM twitter_raw")

    # print all the first cell of all the rows
    for row in cur.fetchall() :
        #print row[0], " ", row[2]

        # tokenize them
        arrayofline = row[2].split(" ")
        tweet_happy_prob, tweet_sad_prob = classifySentiment(arrayofline, happy_log_probs, sad_log_probs)
        sum_sent = tweet_happy_prob
        print sum_sent, " for Tweet:", row[2]


if __name__ == '__main__':
    main()

/******************************************************************************

CAMPSITE is a Unicode-enabled multilingual web content
management system for news publications.
CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.
Copyright (C)2000,2001  Media Development Loan Fund
contact: contact@campware.org - http://www.campware.org
Campware encourages further development. Please let us know.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

******************************************************************************/

/******************************************************************************

Implementation of the classes defined in mutex.h

******************************************************************************/

#include <set>
#include <queue>
#include <unistd.h>

#include "mutex.h"

const int g_nMaxTries = 1000;

// CMutex (default constructor)
CMutex::CMutex()
{
	sem_init(&m_Semaphore, 0, 0);
	m_bLocked = false;
	m_LockingThread = 0;
	m_nLockCnt = 0;
	m_bClosing = false;
	sem_post(&m_Semaphore);
}

// ~CMutex (destructor)
CMutex::~CMutex() throw()
{
	sem_wait(&m_Semaphore);
	m_bClosing = true;
	if (m_bLocked && m_LockingThread == pthread_self())
	{
		m_bLocked = false;
		m_nLockCnt = 0;
	}
	sem_post(&m_Semaphore);
}

// lock: locks mutex
int CMutex::lock() throw(ExMutex)
{
	sem_wait(&m_Semaphore);		// wait for semaphore in order to work with members
	if (m_bClosing)
	{
		sem_post(&m_Semaphore);
		throw ExMutex(MutexSvAbort, "Mutex is closing");
		return 1;
	}
	if (m_bLocked && m_LockingThread == pthread_self())
	{							// if locked by myself just increment the lock counter
		m_nLockCnt++;
		sem_post(&m_Semaphore);
		return 0;
	}
	sem_post(&m_Semaphore);		// release semaphore
	int i;
	for (i = 1; i < g_nMaxTries; i++)
	{
		sem_wait(&m_Semaphore);
		if (!m_bLocked)			// wait until mutex is not locked
			break;
		sem_post(&m_Semaphore);
		usleep(200);
	}
	if (i >= g_nMaxTries)
	{
		sem_post(&m_Semaphore);
		throw ExMutex(MutexSvAbort, "Error locking mutex");
		return 1;
	}
	m_bLocked = true;
	m_LockingThread = pthread_self();
	m_nLockCnt = 1;
	sem_post(&m_Semaphore);
	return 0;
}

// unlock: unlocks mutex
int CMutex::unlock() throw()
{
	sem_wait(&m_Semaphore);
	if (m_bClosing)
	{
		sem_post(&m_Semaphore);
		return 0;
	}
	if (m_bLocked && m_LockingThread == pthread_self())
	{							// if locked by myself decrement lock count
		m_nLockCnt--;
		if (m_nLockCnt == 0)	// when lock count reach 0 unlock mutex
		{
			m_bLocked = false;
		}
	}
	sem_post(&m_Semaphore);
	return 0;
}

/*  CRWMutex description

CRWMutex is an automate with five states (S0-S5). It accepts 4 types of events:
- lock for read (lr)
- lock for write (lw)
- unlock for read (ur)
- unlock for write (uw)

The five states are:
- S0: unlocked for read, unlocked for write
- S1: locked for read, unlocked for write, no write locks scheduled, no read locks scheduled
- S2: unlocked for read, locked for write, no read locks scheduled
	0-many write locks scheduled (after read lock)
- S3: locked for read, unlocked for write, write locks scheduled,
	0-many read locks scheduled (after write lock)
- S4: unlocked for read, locked for write, read locks scheduled,
	0-many write locks scheduled (after read lock)
- S5: unlocked for read, locked for write, write locks scheduled,
	0-many read locks scheduled (after write lock)

Below is the table describing the automate behaviour; lr, lw, ur, uw are events (see the previous
paragraphs) and S0-S5 are the states.

+----+----+----+----+----+----+----+
|    | S0 | S1 | S2 | S3 | S4 | S5 |
+----+----+----+----+----+----+----+
| lr | S1 | S1 | S4 | S3 | S4 | S5 |
+----+----+----+----+----+----+----+
| lw | S2 | S3 | S5 | S3 | S4 | S5 |
+----+----+----+----+----+----+----+
| ur | S0 | S0 | S2 | S4 | S4 | S5 |
+----+----+----+----+----+----+----+
| uw | S0 | S1 | S0 | S3 | S3 | S2 |
+----+----+----+----+----+----+----+

	mutable sem_t m_Semaphore; - it is used to lock access to object members
	bool m_bReadLocked; - true if mutex is locked for read
	bool m_bWriteLocked; - true if mutex is locked for write
	bool m_bClosing; - true if mutex is closing
	CThreadSet* m_pcoReadLocks; - set of current read locks (empty if m_bReadLocked = false - not
		locked for read)
	pthread_t m_nWriteLock; - write lock thread identifier (0 if m_bWriteLocked = false - not
		locked for write)
	CThreadQueue* m_pcoReadQueue; - queue containing threads identifiers scheduled for read locking
	CThreadQueue* m_pcoWriteQueue; - queue containing threads identifiers scheduled for write locking
	CIntQueue* m_pcoScheduler; - queue containing pair of int, bool; if bool = false the next
		thread is scheduled for read locking, otherwise for write locking; the int in the pair
		is a counter of how many locks are scheduled (for read/write)
	mutable pthread_cond_t m_WaitCond; - threads scheduled for locking are in wait state; they
		are woken up when this condition is signaled
	mutable pthread_mutex_t m_CondMutex; - mutex used for m_WaitCond

*/

#ifdef _DEBUG
#define DEBUG_RW_MUTEX(msg) PrintState(msg);
#else
#define DEBUG_RW_MUTEX(msg)
#endif

class CThreadSet : public set<pthread_t> {};
class CThreadQueue : public queue<pthread_t> {};
class CIntQueue : public queue<pair<int, bool> > {};

// CRWMutex constructor; throws ExMutex exception if unable to initialise
CRWMutex::CRWMutex()
{
	sem_init(&m_Semaphore, 0, 0);
	m_bReadLocked = false;
	m_bWriteLocked = false;
	m_bClosing = false;
	m_pcoReadLocks = new CThreadSet;
	m_nWriteLock = 0;
	m_pcoReadQueue = new CThreadQueue;
	m_pcoWriteQueue = new CThreadQueue;
	m_pcoScheduler = new CIntQueue;
	pthread_mutex_init(&m_CondMutex, NULL);
	pthread_cond_init(&m_WaitCond, NULL);
	sem_post(&m_Semaphore);
}

// CRWMutex destructor; destroys the mutex
CRWMutex::~CRWMutex() throw()
{
	sem_wait(&m_Semaphore);
	m_bClosing = true;
	m_bReadLocked = false;
	m_bWriteLocked = false;
	m_nWriteLock = 0;
	delete m_pcoReadQueue;
	delete m_pcoWriteQueue;
	delete m_pcoScheduler;
	pthread_mutex_destroy(&m_CondMutex);
	pthread_cond_destroy(&m_WaitCond);
	sem_post(&m_Semaphore);
}

// lockRead: lock mutex for read
int CRWMutex::lockRead() throw(ExMutex)
{
	sem_wait(&m_Semaphore);
	pthread_t nMyId = pthread_self();
	if (!m_bReadLocked && !m_bWriteLocked)
	{
		m_bReadLocked = true;
		m_pcoReadLocks->insert(nMyId);
		DEBUG_RW_MUTEX("lockRead-S0");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (m_bReadLocked && !m_bWriteLocked)
	{
		if (m_pcoWriteQueue->empty())
		{
			m_pcoReadLocks->insert(nMyId);
			DEBUG_RW_MUTEX("lockRead-S1");
		}
		else
		{
			Schedule(m_pcoReadQueue, nMyId, false);
			DEBUG_RW_MUTEX("lockRead-S3-wait");
			sem_post(&m_Semaphore);
			WaitSchedule(nMyId, false);
			m_bReadLocked = true;
			m_pcoReadLocks->insert(nMyId);
			DEBUG_RW_MUTEX("lockRead-S3-endwait");
		}
		sem_post(&m_Semaphore);
		return 0;
	}
	if (!m_bReadLocked && m_bWriteLocked)
	{
		Schedule(m_pcoReadQueue, nMyId, false);
		DEBUG_RW_MUTEX("lockRead-S2-wait");
		sem_post(&m_Semaphore);
		WaitSchedule(nMyId, false);
		m_bReadLocked = true;
		m_pcoReadLocks->insert(nMyId);
		DEBUG_RW_MUTEX("lockRead-S2-endwait");
		sem_post(&m_Semaphore);
		return 0;
	}
	sem_post(&m_Semaphore);
	return 1;
}

// lockRead: lock mutex for write
int CRWMutex::lockWrite() throw(ExMutex)
{
	sem_wait(&m_Semaphore);
	pthread_t nMyId = pthread_self();
	if (!m_bReadLocked && !m_bWriteLocked)
	{
		m_bWriteLocked = true;
		m_nWriteLock = nMyId;
		DEBUG_RW_MUTEX("lockWrite-S0");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (m_bReadLocked && !m_bWriteLocked)
	{
		Schedule(m_pcoWriteQueue, nMyId, true);
		DEBUG_RW_MUTEX("lockWrite-S1-wait");
		sem_post(&m_Semaphore);
		WaitSchedule(nMyId, true);
		m_bWriteLocked = true;
		m_nWriteLock = nMyId;
		DEBUG_RW_MUTEX("lockWrite-S1-endwait");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (!m_bReadLocked && m_bWriteLocked)
	{
		if (m_nWriteLock != nMyId)
		{
			Schedule(m_pcoWriteQueue, nMyId, true);
			DEBUG_RW_MUTEX("lockWrite-S5-wait");
			sem_post(&m_Semaphore);
			WaitSchedule(nMyId, true);
			m_bWriteLocked = true;
			m_nWriteLock = nMyId;
			DEBUG_RW_MUTEX("lockWrite-S5-endwait");
		}
		else
		{
			DEBUG_RW_MUTEX("lockWrite-S2");
		}
		sem_post(&m_Semaphore);
		return 0;
	}
	sem_post(&m_Semaphore);
	return 1;
}

// unlockRead: unlock mutex for read
int CRWMutex::unlockRead() throw()
{
	sem_wait(&m_Semaphore);
	pthread_t nMyId = pthread_self();
	if (!m_bReadLocked && !m_bWriteLocked)
	{
		DEBUG_RW_MUTEX("unlockRead-S0");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (m_bReadLocked && !m_bWriteLocked)
	{
		m_pcoReadLocks->erase(nMyId);
		if (m_pcoReadLocks->empty())
			m_bReadLocked = false;
		if (!m_pcoWriteQueue->empty() || !m_pcoReadQueue->empty())
		{
			SignalWaitingThreads();
		}
		DEBUG_RW_MUTEX("unlockRead-S1");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (!m_bReadLocked && m_bWriteLocked)
	{
		DEBUG_RW_MUTEX("unlockRead-S2");
		sem_post(&m_Semaphore);
		return 0;
	}
	sem_post(&m_Semaphore);
	return 1;
}

// unlockWrite: unlock mutex for write
int CRWMutex::unlockWrite() throw()
{
	sem_wait(&m_Semaphore);
	pthread_t nMyId = pthread_self();
	if (!m_bReadLocked && !m_bWriteLocked)
	{
		DEBUG_RW_MUTEX("unlockWrite-S0");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (m_bReadLocked && !m_bWriteLocked)
	{
		DEBUG_RW_MUTEX("unlockWrite-S1");
		sem_post(&m_Semaphore);
		return 0;
	}
	if (!m_bReadLocked && m_bWriteLocked)
	{
		if (nMyId == m_nWriteLock)
		{
			m_bWriteLocked = false;
			m_nWriteLock = 0;
			if (!m_pcoReadQueue->empty() || !m_pcoWriteQueue->empty())
			{
				SignalWaitingThreads();
			}
		}
		DEBUG_RW_MUTEX("unlockWrite-S2");
		sem_post(&m_Semaphore);
		return 0;
	}
	sem_post(&m_Semaphore);
	return 1;
}

inline void CRWMutex::Schedule(CThreadQueue* p_pcoQueue, pthread_t p_nThreadId, bool p_bWrite)
{
	p_pcoQueue->push(p_nThreadId);
	if (m_pcoScheduler->empty())
		m_pcoScheduler->push(pair<int, bool>(0, p_bWrite));
	pair<int, bool>& rcoSched = m_pcoScheduler->back();
	if (rcoSched.second != p_bWrite)
		m_pcoScheduler->push(pair<int, bool>(1, p_bWrite));
	else
		rcoSched.first++;
}

void CRWMutex::WaitSchedule(pthread_t p_nThreadId, bool p_bWrite) throw(ExMutex)
{
	while (1)
	{
		pthread_mutex_lock(&m_CondMutex);
		pthread_cond_wait(&m_WaitCond, &m_CondMutex);
		pthread_mutex_unlock(&m_CondMutex);
		sem_wait(&m_Semaphore);
		if ((p_bWrite && m_bReadLocked) || (!p_bWrite && m_bWriteLocked))
		{
			sem_post(&m_Semaphore);
			continue;
		}
		CThreadQueue* pcoQueue = p_bWrite ? m_pcoWriteQueue : m_pcoReadQueue;
		if (m_pcoScheduler->empty() || pcoQueue->empty())
		{
			sem_post(&m_Semaphore);
			throw ExMutex(MutexSvAbort, "scheduler internal error");
		}
		pair<int, bool>& rcoSched = m_pcoScheduler->front();
		if (rcoSched.second != p_bWrite || pcoQueue->front() != p_nThreadId)
		{
			sem_post(&m_Semaphore);
			continue;
		}
		if (--(rcoSched.first) <= 0)
			m_pcoScheduler->pop();
		pcoQueue->pop();
		break;
	}
}

inline void CRWMutex::SignalWaitingThreads() const
{
	pthread_mutex_lock(&m_CondMutex);
	pthread_cond_broadcast(&m_WaitCond);
	pthread_mutex_unlock(&m_CondMutex);
}

inline void CRWMutex::PrintState(const char* p_pchStartMsg) const
{
	cout << p_pchStartMsg << " ";
	cout << pthread_self() << "; read: " << (int)m_bReadLocked << "; write: " << (int)m_bWriteLocked
	     << "; write lock: " << m_nWriteLock << "; read locks: ";
	CThreadSet::const_iterator coIt = m_pcoReadLocks->begin();
	for (; coIt != m_pcoReadLocks->end(); ++coIt)
	{
		if (coIt != m_pcoReadLocks->begin())
			cout << ", ";
		cout << *coIt;
	}
	cout << endl << "\t";
	if (!m_pcoReadQueue->empty())
	{
		cout << "  next read sched: " << m_pcoReadQueue->front();
	}
	if (!m_pcoWriteQueue->empty())
	{
		cout << "  next write sched: " << m_pcoWriteQueue->front();
	}
	if (!m_pcoScheduler->empty())
	{
		bool bWrite = m_pcoScheduler->front().second;
		int nNr = m_pcoScheduler->front().first;
		cout << "; next sched: " << (bWrite ? "write" : "read") << " - " << nNr;
	}
	cout << endl;
}

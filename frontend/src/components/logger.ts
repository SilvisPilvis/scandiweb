// Winston is a Node.js logger and does not work in the browser. We'll provide a fallback for browser logging.
// If you want advanced logging in the browser, consider using libraries like loglevel or console-log-level.

// This logger provides a similar API to winston, but uses the browser console.

export type LogLevel = 'error' | 'warn' | 'info' | 'debug' | 'log';

class Logger {
  error(...args: any[]) {
    console.error('[error]', ...args);
  }
  warn(...args: any[]) {
    console.warn('[warn]', ...args);
  }
  info(...args: any[]) {
    console.info('[info]', ...args);
  }
  debug(...args: any[]) {
    console.debug('[debug]', ...args);
  }
  log(...args: any[]) {
    console.log('[log]', ...args);
  }
}

const logger = new Logger();
export default logger; 